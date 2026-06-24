<?php
/**
 * Cmstack-Laravel
 * File: list.blade.php — revision history for a post/page translation.
 * Shared by posts and pages; the owning controller passes the route names.
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/revisions.headline')</h1>
                <p class="mt-1 text-sm text-ink-500">@lang('cpanel/revisions.subtitle')</p>
            </div>
            <a href="{{ route($edit_route, ['id' => $entity_id, 'lang' => $lang]) }}" class="btn btn-ghost">
                @lang('cpanel/revisions.back_to_editor')
            </a>
        </div>

        @include('cpanel.core.flash')

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table users-table">
                    <thead>
                        <tr>
                            <th class="w-16">@lang('cpanel/revisions.table_revision')</th>
                            <th>@lang('cpanel/revisions.table_author')</th>
                            <th>@lang('cpanel/revisions.table_date')</th>
                            <th class="text-right">@lang('cpanel/revisions.table_actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($revisions as $revision)
                        <tr>
                            <td class="font-medium text-ink-900">v{{ $revisions->total() - ($revisions->firstItem() - 1) - $loop->index }}</td>
                            <td class="text-ink-700">{{ optional($revision->author)->username ?? __('cpanel/revisions.unknown_author') }}</td>
                            <td class="whitespace-nowrap text-ink-600">{{ \Carbon\Carbon::parse($revision->created_at)->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route($diff_route, ['id' => $entity_id, 'revision' => $revision->id, 'lang' => $lang]) }}" class="font-medium text-brand-700 hover:text-brand-800">
                                        @lang('cpanel/revisions.compare')
                                    </a>
                                    <form action="{{ route($restore_route, ['id' => $entity_id, 'revision' => $revision->id, 'lang' => $lang]) }}" method="POST" onsubmit="return confirm('{{ __('cpanel/revisions.restore_confirm') }}');">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost">@lang('cpanel/revisions.restore')</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-ink-400">@lang('cpanel/revisions.empty')</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($revisions->hasPages())
                <div class="border-t border-ink-100 px-5 py-4">
                    {{ $revisions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
