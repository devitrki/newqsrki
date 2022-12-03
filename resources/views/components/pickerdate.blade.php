<fieldset class="form-group position-relative has-icon-left mb-0">
    <input type="text" {{ $attributes->merge(['class' => 'form-control form-control-sm']) }} id="pickerdate{{ $compid.$dom }}" placeholder="{{ __('Select Date') }}">
    <div class="form-control-position">
        <i class="bx bx-calendar"></i>
    </div>
</fieldset>


<script>
    var $input{{ $compid.$dom }} = $("#pickerdate{{$compid.$dom}}").pickadate({
        format: 'dd/mm/yyyy',
        formatSubmit: 'yyyy/mm/dd',
        today: '{{ __("Today") }}',
        close: '{{ __("Close") }}',
        @if($clear == 'true')
        clear: '{{ __("Clear") }}',
        @else
        clear: '',
        @endif
        monthsFull: ['{{ __("January") }}', '{{ __("February") }}', '{{ __("March") }}', '{{ __("April") }}', '{{ __("May") }}', '{{ __("June") }}', '{{ __("July") }}', '{{ __("August") }}', '{{ __("September") }}', '{{ __("October") }}', '{{ __("November") }}', '{{ __("December") }}'],
        weekdaysShort: ['{{ __("Sun") }}', '{{ __("Mon") }}', '{{ __("Tue") }}', '{{ __("Wed") }}', '{{ __("Thu") }}', '{{ __("Fri") }}', '{{ __("Sat") }}'],
    });
    var pickerdate{{ $compid.$dom }} = $input{{ $compid.$dom }}.pickadate('picker');
    
</script>