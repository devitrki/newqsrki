<select {{ $attributes->merge(['class' => 'form-control']) }} id="select2{{ $compid.$dom }}" style="width:50px;">
    {{ $slot }}
</select>
<script>
    @if($type == 'serverside')
        $("#select2{{ $compid.$dom }}").select2({
            width: '100%',
            allowClear: {{ $clear }},
            {!! $getSizeContainer($size) !!}
            @if($dropdowncompid != '')
            dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
            @endif
            ajax: {
                url: "{!! $url !!}",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: false
            }
        });
        @if($default != '')
        $('#select2{{ $compid.$dom }}').val('{{$default}}').trigger('change');
        @endif
    @elseif($type == 'array')
        fslct{{$compid.$dom}} = {
            initWithData: function (data) {
                $("#select2{{$compid.$dom}}").empty().select2({
                    width: '100%',
                    allowClear: {{ $clear }},
                    data: data,
                    {!! $getSizeContainer($size) !!}
                    @if($dropdowncompid != '')
                    dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                    @endif
                });
            }
        }
        fslct{{$compid.$dom}}.initWithData([])
    @else
        $("#select2{{ $compid.$dom }}").select2({
            width: '100%',
            allowClear: {{ $clear }},
            {!! $getSizeContainer($size) !!}
            @if($dropdowncompid != '')
            dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
            @endif
        });
        @if($default != '')
        $('#select2{{ $compid.$dom }}').val('{{$default}}').trigger('change');
        @endif
    @endif
</script>
