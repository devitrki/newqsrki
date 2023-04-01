<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Date">
                                        <x-pickerdate :dom="$dom" compid="fdate" data-value="{{ date('Y/m/d') }}" clear="false" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Shift">
                                        <select class="form-control form-control-sm" id="fshift{{$dom}}">
                                            @php
                                                $options = [
                                                            ['value' => '1', 'text' => 'Shift 1'],
                                                            ['value' => '2', 'text' => 'Shift 2'],
                                                            ['value' => '3', 'text' => 'Shift 3']
                                                        ];
                                            @endphp
                                            @foreach( $options as $i => $opt)
                                                @if($i == 0)
                                                <option value="{{$opt['value']}}" selected>{{$opt['text']}}</option>
                                                @else
                                                <option value="{{$opt['value']}}">{{$opt['text']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </x-row-vertical>
                                </x-form-vertical>
                            </div>
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.filter()">
                                    <span>{{ __('Filter') }}</span>
                                </button>
                            </div>
                        </x-dropdown-filter>
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                    [[
                        'label' => 'date',
                        'data' => 'date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'daily task',
                        'data' => 'task',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'section',
                        'data' => 'section',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'frekuensi',
                        'data' => 'frekuensi',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];

                foreach ($shift1 as $is => $shift) {
                    $columns[] = [
                        'label' => $shift,
                        'data' => 'checklis_' . ($is + 1) . '_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input text-center'
                    ];
                }

                $columns[] = [
                    'label' => 'pic',
                    'data' => 'pic_input',
                    'searchable' => 'false',
                    'orderable' => 'false',
                    'class' => 'input'
                ];

            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/daily-cleaning/dtble?plant-id={{$first_plant_id}}&date={{ date('Y/m/d') }}&shift=1" />
        </div>
    </div>
</x-card-scroll>

<script>
function changeLbDlyCleaning(id, field) {
    var value = '';

    if( field != 'pic' ){
        check = $("#lbdlycleaning" + field + id).is(':checked');
        value = (check) ? 1 : 0;
    } else {
        value = $("#lbdlycleaning" + field + id).val();
    }

    var data = {
        'field': field,
        'value': value,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Daily Cleaning and Sanitation');
        } else {
            toastr.error(res.message, 'Update Daily Cleaning and Sanitation');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        id: 0,
        checklist: 0,
        shift1: @JSON($shift1),
        shift2: @JSON($shift2),
        shift3: @JSON($shift3),
    },
    url: {
        save: "logbook/daily-cleaning",
        datatable: "logbook/daily-cleaning/dtble"
    },
    event: {
    },
    func: {
        filter: function () {
            var shift = $("#fshift{{$dom}}").val();
            // change name column
            if(shift == 1){
                for (let iCol = 0; iCol <= 7; iCol++) {
                    fdtbletabledata{{$dom}}.changeNameColumn(iCol+5, {{$dom}}.data.shift1[iCol]);
                }
            } else if(shift == 2) {
                for (let iCol = 0; iCol <= 7; iCol++) {
                    fdtbletabledata{{$dom}}.changeNameColumn(iCol+5, {{$dom}}.data.shift2[iCol]);
                }
            } else {
                for (let iCol = 0; iCol <= 7; iCol++) {
                    fdtbletabledata{{$dom}}.changeNameColumn(iCol+5, {{$dom}}.data.shift3[iCol]);
                }
            }
            fdtbletabledata{{$dom}}.draw();

            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd') + '&shift=' + shift;
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        }
    }
}

</script>
