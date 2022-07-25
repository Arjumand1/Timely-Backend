@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => "https://www.crunchbase.com/organization/vast-mesh"])
            {{-- {{ config('app.name') }} --}}
            <img src="https://upwork-usw2-prod-assets-static.s3.us-west-2.amazonaws.com/org-logo/1512385156589019136" width="100px" height="100px" alt="vastmesh">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
           <b style="color:rgba(251, 64, 64, 0.895)"> © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')</b>
        @endcomponent
    @endslot
@endcomponent
