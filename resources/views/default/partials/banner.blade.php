<?php
/**
 * Cmstack-Laravel — shared page banner / breadcrumb partial (Phase 4).
 *
 * @param string      $title        Heading text.
 * @param array|null  $crumbs       Optional breadcrumb items: [['label' => .., 'url' => ..|null], ...]
 *                                  The last item with url === null renders as current page.
 */
?>
<header class="border-b border-ink-100 bg-ink-50/60">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 sm:py-16">
        @if(!empty($crumbs))
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm text-ink-400">
                    @foreach($crumbs as $crumb)
                        <li class="flex items-center gap-1.5">
                            @if(!empty($crumb['url']))
                                <a href="{{$crumb['url']}}" class="transition-colors hover:text-brand-700">{{$crumb['label']}}</a>
                                <svg class="h-3.5 w-3.5 text-ink-300" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m8 5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <span class="text-ink-600">{{$crumb['label']}}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
        <h1 class="text-3xl font-medium tracking-tight text-ink-900 sm:text-4xl lg:text-5xl [text-wrap:balance]">{{$title}}</h1>
    </div>
</header>
