@component('mail::message')
# Hello {{$name->name}},

You can download our application from

@component('mail::button', ['url' => 'http://attendance.vastmesh.com/en/timesheet/'])
Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
