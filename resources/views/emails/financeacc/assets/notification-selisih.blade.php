<x-mail::message>
{{ __('Dear') }} {{ $dear }},

{{ __('Stock opname asset period') . ' ' . $periode . ' ' . __('has been completed')}}

{{ __('Difference in asset stock taking is attached') }}.
<br/>
<br/>
{{ __('Thank You') }}
</x-mail::message>
