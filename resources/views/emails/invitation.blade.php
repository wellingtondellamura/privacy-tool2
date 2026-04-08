@component('mail::message')
# {{ __('email.invitation_heading') }}

{!! __('email.invitation_body', ['project' => $project->name]) !!}

{!! __('email.invitation_role', ['role' => __('labels.roles.' . $invitation->role)]) !!}

@if($hasAccount)
{{ __('email.invitation_existing_user') }}

@component('mail::button', ['url' => route('login')])
{{ __('email.invitation_login_button') }}
@endcomponent
@else
{{ __('email.invitation_new_user') }}

@component('mail::button', ['url' => route('register')])
{{ __('email.invitation_register_button') }}
@endcomponent
@endif

{{ __('email.invitation_ignore') }}

{{ __('email.thanks') }}<br>
{{ __('email.team', ['app' => config('app.name')]) }}
@endcomponent
