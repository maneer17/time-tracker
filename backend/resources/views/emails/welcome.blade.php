@component('mail::message')

# Welcome to {{ config('app.name') }}, {{ $user->name }}!

We're glad to have you on board.

Here's what you can do to get started:

@component('mail::panel')
- Create your first channel
- Invite your friends
- Share your first day
@endcomponent

@component('mail::button', ['url' => config('app.frontend_url')])
Get Started
@endcomponent

If you have any questions, feel free to reach out.

Regards,
{{ config('app.name') }}

@endcomponent