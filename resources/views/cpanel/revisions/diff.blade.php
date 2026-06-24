<?php
/**
 * Cmstack-Laravel
 * File: diff.blade.php — per-field comparison of one revision vs the live row.
 * Shared by posts and pages; values are escaped (snapshots may contain HTML).
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/revisions.diff_headline')</h1>
                <p class="mt-1 text-sm text-ink-500">@lang('cpanel/revisions.diff_subtitle')</p>
                <p class="mt-1 text-xs text-ink-400">{{ \Carbon\Carbon::parse($revision->created_at)->format('d.m.Y H:i') }}</p>
            </div>
            <a href="{{ route($list_route, ['id' => $entity_id, 'lang' => $lang]) }}" class="btn btn-ghost">
                @lang('cpanel/revisions.back_to_list')
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-40">@lang('cpanel/revisions.diff_field')</th>
                            <th>@lang('cpanel/revisions.diff_old')</th>
                            <th>@lang('cpanel/revisions.diff_current')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($fields as $field)
                        <tr class="{{ $field['changed'] ? 'bg-amber-50' : '' }}">
                            <td class="align-top">
                                <span class="font-medium text-ink-900">{{ $field['field'] }}</span>
                                @if($field['changed'])
                                    <span class="badge badge-warning mt-1 block w-fit">@lang('cpanel/revisions.diff_changed')</span>
                                @else
                                    <span class="badge badge-muted mt-1 block w-fit">@lang('cpanel/revisions.diff_unchanged')</span>
                                @endif
                            </td>
                            <td class="align-top">
                                <div class="max-h-64 overflow-auto whitespace-pre-wrap break-words text-sm text-ink-700">{{ $field['old'] }}</div>
                            </td>
                            <td class="align-top">
                                <div class="max-h-64 overflow-auto whitespace-pre-wrap break-words text-sm text-ink-700">{{ $field['current'] }}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                <form action="{{ route($restore_route, ['id' => $entity_id, 'revision' => $revision->id, 'lang' => $lang]) }}" method="POST" onsubmit="return confirm('{{ __('cpanel/revisions.restore_confirm') }}');">
                    @csrf
                    <button type="submit" class="btn btn-info">@lang('cpanel/revisions.restore')</button>
                </form>
            </div>
        </div>
    </div>
@endsection
