<?php
/**
 * Laravella CMS
 * File: modal.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 07.11.2019
 * Phase 4: rewritten from Bootstrap modal to a native <dialog> + Alpine.
 *          Opened via the `open-edit-comment` window event dispatched by the
 *          comment "Edit" buttons. Submits to update_post_comment (PUT).
 */
?>

<div
    x-data="editCommentDialog()"
    @open-edit-comment.window="show($event.detail)"
>
    <dialog
        x-ref="dialog"
        @close="open = false"
        @click.self="close()"
        class="m-auto w-[min(92vw,32rem)] rounded-2xl border border-ink-100 bg-surface p-0 text-ink-800 shadow-lift backdrop:bg-ink-950/40 backdrop:backdrop-blur-sm"
    >
        <form action="{{route('update_post_comment')}}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4">
                <h2 class="font-serif text-xl font-medium text-ink-900">@lang('default/modal.edit_comment')</h2>
                <button type="button" @click="close()" aria-label="Close"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-ink-400 transition hover:bg-ink-50 hover:text-ink-700">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 5 10 10M15 5 5 15" stroke-linecap="round"/></svg>
                </button>
            </div>

            <div class="px-6 py-5">
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <textarea x-ref="field" id="comment-update-field" name="comment" rows="5" required
                          placeholder="@lang('default/post.comment')"
                          class="field-input resize-y"></textarea>
                <input type="hidden" x-ref="id" name="updated_comment_id" id="updated_comment_id" value="">
            </div>

            <div class="flex justify-end gap-3 border-t border-ink-100 px-6 py-4">
                <button type="submit" class="btn-primary">@lang('default/modal.update_comment_btn')</button>
            </div>
        </form>
    </dialog>
</div>
