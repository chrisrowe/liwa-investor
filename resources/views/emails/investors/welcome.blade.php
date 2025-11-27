@component('mail::message')

Dear {{ $investor->name }},

You have been invited to access our secure virtual data room.

Please click the link below to set up your account and view the materials.

@component('mail::button', ['url' => $resetPasswordUrl])
	Set Password
@endcomponent

To access the portal in the future please visit our website at <a href="https://liwacap.com">www.liwacap.com</a> and click the "Investor Login" button in the top right corner.

Thank you,
<br/>
<br/>
{{ config('app.name') }}
@endcomponent
