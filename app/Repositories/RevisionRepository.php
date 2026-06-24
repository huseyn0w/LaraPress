<?php

namespace App\Repositories;

use App\Http\Models\Revision;
use Illuminate\Database\Eloquent\Model;

/**
 * Persistence for content revisions. All ORM access for revisions lives here so
 * observers/services only delegate (mirrors TagRepository). A revision is an
 * immutable snapshot of a translation row's attributes taken before an update.
 */
class RevisionRepository extends BaseRepository
{
    // Revisions have no translations or author eager-load needs of their own.
    protected $eager_relations = [];

    /**
     * Volatile attributes never restored back onto a live row — they identify
     * the row (id/join column/locale) or are independent counters (likes), not
     * the editorial content a revision is meant to capture.
     *
     * @var array<int, string>
     */
    private array $excludedFromRestore = [
        'id', 'created_at', 'updated_at', 'post_id', 'page_id', 'locale', 'likes',
    ];

    public function __construct(Revision $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Snapshot a translation row's PRE-update state. Called from the *updating*
     * observer hook, where getOriginal() still returns the persisted (old)
     * values. New (never-persisted) rows are skipped — there is nothing to
     * preserve before a first insert.
     */
    public function snapshot(Model $translation): void
    {
        if (! $translation->exists) {
            return;
        }

        $this->model->create([
            'revisionable_type' => $translation->getMorphClass(),
            'revisionable_id' => $translation->getKey(),
            'user_id' => auth()->id(),
            'data' => $translation->getOriginal(),
        ]);
    }

    /**
     * All revisions for a translation row, newest first, with the editor loaded.
     */
    public function listFor(string $type, int $id)
    {
        return $this->model
            ->where('revisionable_type', $type)
            ->where('revisionable_id', $id)
            ->with('author')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * A single revision scoped to its translation row (guards against fetching a
     * revision that belongs to a different post/page).
     */
    public function findFor(string $type, int $id, int $revisionId): ?Revision
    {
        return $this->model
            ->where('revisionable_type', $type)
            ->where('revisionable_id', $id)
            ->whereKey($revisionId)
            ->first();
    }

    /**
     * The content payload of a revision, stripped of volatile/identity keys, in
     * a shape safe to write back onto the live translation row.
     *
     * @return array<string, mixed>
     */
    public function restorableData(Revision $revision): array
    {
        return collect($revision->data)
            ->except($this->excludedFromRestore)
            ->all();
    }

    /**
     * Per-field comparison between a revision's snapshot and the live row, for
     * the compare screen. Each entry carries the field name, the snapshot
     * value, the current value and whether they differ. Pure presentation data —
     * no ORM.
     *
     * @return array<int, array{field: string, old: mixed, current: mixed, changed: bool}>
     */
    public function diff(Model $current, Revision $revision): array
    {
        $fields = [];

        foreach ($this->restorableData($revision) as $field => $oldValue) {
            $currentValue = $current->getAttribute($field);

            $fields[] = [
                'field' => $field,
                'old' => $oldValue,
                'current' => $currentValue,
                'changed' => (string) $oldValue !== (string) $currentValue,
            ];
        }

        return $fields;
    }

    /**
     * Restore a revision: write its snapshot content back onto the live
     * translation row. The save fires the translation's *updating* observer,
     * which snapshots the current (pre-restore) state first — so a restore is
     * itself revisioned and therefore undoable. Returns false when the row no
     * longer exists.
     */
    public function restoreFrom(Revision $revision): bool
    {
        $translation = $revision->revisionable;

        if (! $translation) {
            return false;
        }

        $translation->fill($this->restorableData($revision));

        return (bool) $translation->save();
    }
}
