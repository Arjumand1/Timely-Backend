@component('mail::message')
# Hello {{$name->name}},

You can download our application from

@component('mail::button', ['url' => 'http://google.com'])
Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
