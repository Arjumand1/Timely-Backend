@component('mail::message')
# Hello {{$name->name}}

You can login into our app by using following credentials

<b>Email:</b>{{$name->email}} <br>
<br>
<b>Password:{{$name->password}}</b>

<b>Note:</b>
<p style="color: red">please change your password before login</p>

You can download our application from here

@component('mail::button', ['url' => ''])
Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
