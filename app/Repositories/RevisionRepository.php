<?php

namespace App\Repositories;

use App\Http\Models\Revision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
     * Editorial fields a restore may write back onto the live translation row.
     * This is an ALLOW-list, not a deny-list: anything else in the snapshot
     * (identity columns id/post_id/page_id/locale, the `likes` counter, the
     * `author_id` — never silently reassign authorship — timestamps, or any
     * stray/forged key) is dropped, so fill() can only touch known content.
     *
     * @var array<int, string>
     */
    private array $restorableFields = [
        'title', 'slug', 'content', 'preview', 'template', 'custom_fields',
        'status', 'meta_description', 'meta_keywords', 'meta_noindex', 'canonical_url',
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
     * Paginated revisions for a translation row, newest first, editor eager-
     * loaded. Paginated so a heavily-edited post can't load its entire (full-row
     * JSON) history into one page.
     */
    public function listFor(string $type, int $id, int $perPage = 25)
    {
        return $this->model
            ->where('revisionable_type', $type)
            ->where('revisionable_id', $id)
            ->with('author')
            ->orderByDesc('id')
            ->paginate($perPage);
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
            ->only($this->restorableFields)
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

        try {
            // Atomic: the pre-restore snapshot (taken by the updating observer)
            // and the row update commit together, so a failed restore — e.g. a
            // unique(locale,title,slug) collision — leaves no orphaned revision.
            return (bool) DB::transaction(fn () => $translation->save());
        } catch (QueryException $e) {
            return false;
        }
    }
}
