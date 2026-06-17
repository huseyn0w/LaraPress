@extends(config('app.template_name').'/index')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">@lang('email.verify_page_headline')</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            @lang('email.fresh_link')
                        </div>
                    @endif

                        @lang('email.check_email')
                        @lang('email.not_receive_email'), <a href="{{ route('verification.resend') }}">@lang('email.request_other_email')</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
