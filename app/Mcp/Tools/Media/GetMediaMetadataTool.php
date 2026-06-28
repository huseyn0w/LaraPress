<?php

namespace App\Mcp\Tools\Media;

use App\Mcp\Concerns\AuthorizesAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Get metadata for a single media file by its relative path within the uploads folder. Returns size, mime type, url, modified time, and image dimensions when applicable. Requires the manage_general_settings permission.')]
class GetMediaMetadataTool extends Tool
{
    use AuthorizesAccess;

    /** Absolute path to the media root (public/uploads). */
    protected function mediaRoot(): string
    {
        return realpath(public_path('uploads')) ?: public_path('uploads');
    }

    /**
     * Resolve a caller-supplied relative file path to an absolute path that is
     * guaranteed to live inside the media root.
     *
     * @return string|null Absolute safe path, or null if the path is rejected.
     */
    protected function safeMediaFilePath(string $relative): ?string
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

        if (! is_file($real)) {
            return null;
        }

        return $real;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'path' => $schema->string()
                ->description('Relative path to the file within the uploads folder, e.g. "images/photo.jpg". No ".." or absolute paths.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'path' => ['required', 'string', 'max:1024'],
        ]);

        $absolute = $this->safeMediaFilePath($validated['path']);

        if (is_null($absolute)) {
            return Response::error('Not found or rejected path. Provide an existing file inside the uploads folder (no absolute paths or "..").');
        }

        $mime = mime_content_type($absolute) ?: null;
        $size = filesize($absolute);
        $modified = date('c', filemtime($absolute));

        $root = $this->mediaRoot();
        $relativePath = ltrim(str_replace($root, '', $absolute), DIRECTORY_SEPARATOR);
        $relativeUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        $url = rtrim(url('uploads'), '/').'/'.$relativeUrl;

        $dimensions = null;

        if ($mime !== null && str_starts_with($mime, 'image/') && ! str_contains($mime, 'svg')) {
            $info = @getimagesize($absolute);

            if ($info !== false) {
                $dimensions = [
                    'width' => $info[0],
                    'height' => $info[1],
                ];
            }
        }

        return Response::structured([
            'name' => basename($absolute),
            'path' => $relativeUrl,
            'size' => $size,
            'mime' => $mime,
            'url' => $url,
            'modified' => $modified,
            'dimensions' => $dimensions,
        ]);
    }
}
