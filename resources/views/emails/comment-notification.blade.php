@component('mail::message')
# New comment awaiting moderation

@isset($postTitle)
A new comment was submitted on **{{ $postTitle }}**.
@else
A new comment was submitted.
@endisset

**{{ $authorName }}** wrote:

> {{ $commentBody }}

@component('mail::button', ['url' => $moderateUrl])
Review comments
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
