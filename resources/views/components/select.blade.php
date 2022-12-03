<select {{ $attributes->merge(['class' => 'form-control select2-size-sm']) }} id="select2{{ $compid.$dom }}" style="width:50px;">
    {{ $slot }}
</select>
<script>
    fslct{{$compid.$dom}} = {
        url: "{!! $url !!}",
        refresh: function () {
            @if($type == 'serverside')
                @if($async == 'true')
                    $("#select2{{ $compid.$dom }}").select2({
                        width: '100%',
                        allowClear: {{ $clear }},
                        {!! $getSizeContainer($size) !!}
                        @if($dropdowncompid != '')
                        dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                        @endif
                        ajax: {
                            url: fslct{{$compid.$dom}}.url,
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
                            cache: {{ $cache }}
                        },
                        @if($autocomplete == 'true')
                        minimumInputLength: 1,
                        language: {
                            inputTooShort: function () {
                                return "{{ __('Please enter 1 or more characters') }}";
                            }
                        }
                        @endif
                    });
                @else
                    $.get( fslct{{$compid.$dom}}.url, function (res) {
                        $("#select2{{$compid.$dom}}").select2({
                            width: '100%',
                            allowClear: {{ $clear }},
                            data: res,
                            {!! $getSizeContainer($size) !!}
                            @if($dropdowncompid != '')
                            dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                            @endif
                        });
                        @empty($default)
                        @else
                        $("#select2{{$compid.$dom}}").val({{ $default[0] }}).trigger('change');
                        @endempty
                    });
                @endif

            @else
                $("#select2{{ $compid.$dom }}").select2({
                    width: '100%',
                    allowClear: {{ $clear }},
                    {!! $getSizeContainer($size) !!}
                    @if($dropdowncompid != '')
                    dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                    @endif
                });
                @empty($default)
                @else
                $("#select2{{$compid.$dom}}").val({{ $default[0] }}).trigger('change');
                @endempty
            @endif
        },
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
        },
        set: function (id, text) {
            if (id !== '' && id !== 'null' && id !== null) {
                @if($type == 'serverside' && $async == 'true')
                    var option = new Option(text, id, true, true);
                    $("#select2{{$compid.$dom}}").append(option).trigger('change');
                @else
                    $("#select2{{$compid.$dom}}").val(id).trigger('change');
                @endif
            }
        },
        get: function () {
            var selected = $("#select2{{$compid.$dom}}").val();
            return selected;
        },
        clear: function () {
            $('#select2{{$compid.$dom}}').val(null).trigger('change');
        }
    }

    @if($type == 'serverside')
        @if($async == 'true')
            $("#select2{{ $compid.$dom }}").select2({
                width: '100%',
                allowClear: {{ $clear }},
                {!! $getSizeContainer($size) !!}
                @if($dropdowncompid != '')
                dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                @endif
                ajax: {
                    url: fslct{{$compid.$dom}}.url,
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
                    cache: {{ $cache }}
                },
                @if($autocomplete == 'true')
                minimumInputLength: 1,
                language: {
                    inputTooShort: function () {
                        return "{{ __('Please enter 1 or more characters') }}";
                    }
                }
                @endif
            });
        @else
            $.get( fslct{{$compid.$dom}}.url, function (res) {
                $("#select2{{$compid.$dom}}").select2({
                    width: '100%',
                    allowClear: {{ $clear }},
                    data: res,
                    {!! $getSizeContainer($size) !!}
                    @if($dropdowncompid != '')
                    dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
                    @endif
                });
                @empty($default)
                @else
                $("#select2{{$compid.$dom}}").val({{ $default[0] }}).trigger('change');
                @endempty
            });
        @endif

    @else
        $("#select2{{ $compid.$dom }}").select2({
            width: '100%',
            allowClear: {{ $clear }},
            {!! $getSizeContainer($size) !!}
            @if($dropdowncompid != '')
            dropdownParent: $('#hfilter{{$dropdowncompid.$dom}}'),
            @endif
        });
        @empty($default)
        @else
        $("#select2{{$compid.$dom}}").val({{ $default[0] }}).trigger('change');
        @endempty
    @endif

    @if($type == 'array')
        fslct{{$compid.$dom}}.initWithData(@json($options));
    @endif

    @if( in_array($type, ['array', 'serverside']))
        @empty($default)
        @else
        fslct{{$compid.$dom}}.set('{{ $default[0] }}' , '{{ $default[1] }}');
        @endempty
    @endif
</script>
