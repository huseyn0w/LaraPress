<?php

namespace App\Mcp\Tools\Posts;

use App\Http\Models\Post;
use App\Http\Models\PostTranslation;
use App\Mcp\Concerns\AuthorizesAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Publish a post immediately by setting its status to published. Optionally target a single locale; when omitted all translations of the post are published. Clears any pending schedule. Requires the manage_posts permission.')]
class PublishPostTool extends Tool
{
    use AuthorizesAccess;

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The post id to publish.')
                ->required(),
            'locale' => $schema->string()
                ->description('Language code of the specific translation to publish, e.g. "en". When omitted all translations are published.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
        ]);

        $postId = (int) $validated['id'];
        $locale = $validated['locale'] ?? null;

        $post = Post::find($postId);

        if (is_null($post)) {
            return Response::error("No post found with id {$postId}.");
        }

        $query = PostTranslation::where('post_id', $postId);

        if (! is_null($locale)) {
            $query->where('locale', $locale);
        }

        $translations = $query->get();

        if ($translations->isEmpty()) {
            $msg = is_null($locale)
                ? "Post {$postId} has no translations to publish."
                : "Post {$postId} has no '{$locale}' translation to publish.";

            return Response::error($msg);
        }

        $published = 0;

        foreach ($translations as $translation) {
            $translation->status = Post::STATUS_PUBLISHED;
            $translation->scheduled_at = null;

            if ($translation->save()) {
                $published++;
            }
        }

        return $published > 0
            ? Response::structured([
                'published' => true,
                'id' => $postId,
                'translations_published' => $published,
                'locale' => $locale,
            ])
            : Response::error("Could not publish post {$postId}.");
    }
}
