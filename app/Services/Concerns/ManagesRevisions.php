<?php

namespace App\Services\Concerns;

use App\Http\Models\Revision;
use App\Repositories\RevisionRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Revision listing / diff / restore for translatable content services
 * (posts, pages). The host service supplies its content repository via
 * BaseCrudService::$repository (used to resolve the translation row for a
 * locale) and a RevisionRepository via $this->revisions. Revisions are keyed
 * on the translation row, so this works for any translatable entity without
 * hard-coding the translation class.
 */
trait ManagesRevisions
{
    protected RevisionRepository $revisions;

    /**
     * The current translation plus its revisions (newest first) for the
     * revision-history screen. `current` is null when the entity/locale has no
     * translation.
     *
     * @return array{current: ?Model, revisions: Collection}
     */
    public function revisionsFor(int $id, string $locale): array
    {
        $translation = $this->repository->translationFor($id, $locale);

        if (! $translation) {
            return ['current' => null, 'revisions' => collect()];
        }

        return [
            'current' => $translation,
            'revisions' => $this->revisions->listFor($translation->getMorphClass(), (int) $translation->getKey()),
        ];
    }

    /**
     * The current translation alongside a single revision and a per-field diff,
     * for the compare screen. Null when the translation or the (scoped)
     * revision does not exist.
     *
     * @return array{current: Model, revision: Revision, fields: array<int, array<string, mixed>>}|null
     */
    public function revisionDiff(int $id, string $locale, int $revisionId): ?array
    {
        $translation = $this->repository->translationFor($id, $locale);

        if (! $translation) {
            return null;
        }

        $revision = $this->revisions->findFor($translation->getMorphClass(), (int) $translation->getKey(), $revisionId);

        if (! $revision) {
            return null;
        }

        return [
            'current' => $translation,
            'revision' => $revision,
            'fields' => $this->revisions->diff($translation, $revision),
        ];
    }

    /**
     * Restore a revision scoped to this entity+locale. Returns false when the
     * translation or the revision (under this translation) is absent — the
     * controller maps that to a 404 so a revision can never be applied to a
     * different post/page.
     */
    public function restoreRevision(int $id, string $locale, int $revisionId): bool
    {
        $translation = $this->repository->translationFor($id, $locale);

        if (! $translation) {
            return false;
        }

        $revision = $this->revisions->findFor($translation->getMorphClass(), (int) $translation->getKey(), $revisionId);

        if (! $revision) {
            return false;
        }

        return $this->revisions->restoreFrom($revision);
    }
}
