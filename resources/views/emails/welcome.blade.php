@component('mail::message')
# Hello {{$name->name}}

You can login into our app by using following credentials

<b>Email:</b>{{$name->email}} <br>

You can download our application from here

@component('mail::button', ['url' => ''])
Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
