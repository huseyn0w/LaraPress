<?php
/**
 * Cmstack-Laravel — menu builder source accordion (Phase 5 partial).
 * Shared by new_menu / edit_menu. The select IDs + classes
 * (#pages_list / #posts_list / #categories_list, .multiple_list.menu_item,
 * #link_label / #link_url) are the hooks menu.js reads, so they are preserved.
 * The accordion uses data-toggle="collapse" handled by resources/js/admin.js.
 */
?>
<div class="accordion mt-2" id="menusAccordion">
    {{-- Pages --}}
    <div class="card">
        <div class="card-header" id="headingOne">
            <button class="" type="button" data-toggle="collapse" data-target="#pages_tab" aria-expanded="false">
                @lang('cpanel/menus.pages')
                <svg class="h-4 w-4 text-ink-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <div id="pages_tab" class="collapse">
            <div class="px-4 py-3">
                <select name="pages" multiple class="form-control multiple_list menu_item" id="pages_list" data-type="pages">
                    @forelse($terms_list['pages'] as $page)
                        <option value="{{$page->slug}}">{{$page->title}}</option>
                    @empty
                        <option disabled>@lang('cpanel/menus.no_pages')</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {{-- Posts --}}
    <div class="card">
        <div class="card-header" id="headingTwo">
            <button class="" type="button" data-toggle="collapse" data-target="#posts_tab" aria-expanded="false">
                @lang('cpanel/menus.posts')
                <svg class="h-4 w-4 text-ink-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <div id="posts_tab" class="collapse">
            <div class="px-4 py-3">
                <select name="posts" multiple class="form-control multiple_list menu_item" id="posts_list" data-type="posts">
                    @forelse($terms_list['posts'] as $post)
                        <option value="{{$post->slug}}">{{$post->title}}</option>
                    @empty
                        <option disabled>@lang('cpanel/menus.no_posts')</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="card">
        <div class="card-header" id="headingThree">
            <button class="" type="button" data-toggle="collapse" data-target="#categories_tab" aria-expanded="false">
                @lang('cpanel/menus.categories')
                <svg class="h-4 w-4 text-ink-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <div id="categories_tab" class="collapse">
            <div class="px-4 py-3">
                <select name="category" multiple class="form-control multiple_list menu_item" id="categories_list" data-type="categories">
                    @forelse($terms_list['categories'] as $category)
                        <option value="{{$category->slug}}">{{$category->title}}</option>
                    @empty
                        <option disabled>@lang('cpanel/menus.no_categories')</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {{-- Custom link --}}
    <div class="card">
        <div class="card-header" id="headingFour">
            <button class="" type="button" data-toggle="collapse" data-target="#custom_link_tap" aria-expanded="false">
                @lang('cpanel/menus.custom_link')
                <svg class="h-4 w-4 text-ink-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <div id="custom_link_tap" class="collapse">
            <div class="space-y-3 px-4 py-3">
                <div class="field">
                    <label for="link_label" class="field-label">@lang('cpanel/menus.custom_link_label')</label>
                    <input type="text" id="link_label" class="form-control menu_item" name="link_label">
                </div>
                <div class="field">
                    <label for="link_url" class="field-label">@lang('cpanel/menus.custom_link_url')</label>
                    <input type="text" id="link_url" class="form-control menu_item" name="link_url">
                </div>
            </div>
        </div>
    </div>
</div>
