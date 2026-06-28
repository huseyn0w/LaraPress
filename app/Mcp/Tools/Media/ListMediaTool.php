<?php

namespace App\Mcp\Tools\Media;

use App\Mcp\Concerns\AuthorizesAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Symfony\Component\Finder\Finder;

#[Description('Read-only. List media files stored in the LFM uploads directory. Returns name, relative path, size, mime type, url, and modified time. Supports an optional subdirectory and pagination. Requires the manage_general_settings permission.')]
class ListMediaTool extends Tool
{
    use AuthorizesAccess;

    /** Absolute path to the media root (public/uploads). */
    protected function mediaRoot(): string
    {
        return realpath(public_path('uploads')) ?: public_path('uploads');
    }

    /**
     * Resolve a caller-supplied subdirectory to an absolute path that is
     * guaranteed to live inside the media root.
     *
     * @return string|null Absolute safe path, or null if the path is rejected.
     */
    protected function safeMediaPath(string $relative): ?string
    {
        if ($relative === '' || str_contains($relative, "\0") || str_contains($relative, '..')) {
            return null;
        }

        if (str_starts_with($relative, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $relative)) {
            return null;
        }

        $root = $this->mediaRoot();
        $candidate = $root.DIRECTORY_SEPARATOR.ltrim($relative, '/\\');
        $real = realpath($candidate);

        if (! $real || ! str_starts_with($real, $root.DIRECTORY_SEPARATOR)) {
            return null;
        }

        if (! is_dir($real)) {
            return null;
        }

        return $real;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'subdirectory' => $schema->string()->description('Optional subdirectory within the uploads folder, e.g. "images". No ".." or absolute paths.'),
            'limit' => $schema->integer()->description('Maximum number of files to return (1-200). Defaults to 50.'),
            'offset' => $schema->integer()->description('Zero-based offset for pagination. Defaults to 0.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'subdirectory' => ['nullable', 'string', 'max:512'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ]);

        $root = $this->mediaRoot();

        if (! is_dir($root)) {
            return Response::error('The media uploads directory does not exist.');
        }

        $searchDir = $root;

        if (! empty($validated['subdirectory'])) {
            $searchDir = $this->safeMediaPath($validated['subdirectory']);

            if (is_null($searchDir)) {
                return Response::error('Rejected subdirectory. Provide a relative path inside the uploads folder (no absolute paths or "..").');
            }
        }

        $limit = (int) ($validated['limit'] ?? 50);
        $offset = (int) ($validated['offset'] ?? 0);

        $finder = Finder::create()->files()->in($searchDir)->sortByName();
        $allFiles = iterator_to_array($finder, false);
        $total = count($allFiles);
        $slice = array_slice($allFiles, $offset, $limit);

        $baseUrl = rtrim(url('uploads'), '/');
        $files = [];

        foreach ($slice as $file) {
            $realPath = $file->getRealPath();
            $relativePath = ltrim(str_replace($root, '', $realPath), DIRECTORY_SEPARATOR);
            $relativeUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            $mime = mime_content_type($realPath) ?: null;

            $files[] = [
                'name' => $file->getFilename(),
                'path' => $relativeUrl,
                'size' => $file->getSize(),
                'mime' => $mime,
                'url' => $baseUrl.'/'.$relativeUrl,
                'modified' => date('c', $file->getMTime()),
            ];
        }

        return Response::structured([
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'files' => $files,
        ]);
    }
}
