<style>
    .custom-switch .custom-control-label:before {
        background-color: red;
    }
</style>
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
                        'label' => 'storage name',
                        'data' => 'name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];

                foreach ($range_check_temp as $is => $temp) {
                    $columns[] = [
                        'label' => $temp,
                        'data' => 'temp_' . ($is + 1) . '_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ];
                }

                $columns[] = [
                    'label' => 'note',
                    'data' => 'note_input',
                    'searchable' => 'false',
                    'orderable' => 'false',
                    'class' => 'input'
                ];

            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/operational/temperature/dtble?plant-id={{$first_plant_id}}&date={{ date('Y/m/d') }}"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Temperature Form">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Storage">
                    <input type="text" class="form-control form-control-sm" id="storage{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-row-vertical label="note">
                    <textarea class="form-control form-control-sm" id="note{{$dom}}" rows="3"></textarea>
                </x-row-vertical>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Temperature')" class="mt-0" />
            </div>
            @foreach ($range_check_temp as $is => $temp)
            <div class="col-12 col-md-6">
                <x-row-horizontal label="{{$temp}}">
                    <select class="form-control form-control-sm" id="temp_{{ ($is+1) . $dom }}">
                    </select>
                </x-row-horizontal>
            </div>
            @endforeach
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.save()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->
<script>
function changeLbTemp(id, field) {
    var data = "";
    if( field == 'note' ){
        data = $("#lbtempnote" + id).val();
    } else {
        data = $("#lb" + field + id).val();
    }

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Temperature');
        } else {
            toastr.error(res.message, 'Update Temperature');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "logbook/operational/temperature",
        datatable: "logbook/operational/temperature/dtble"
    },
    event: {
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        set: function (data) {
            {{$dom}}.data.id = data.id;

            $("#storage{{$dom}}").val(data.name);
            $("#note{{$dom}}").val(data.note);

            // change option
            $('#temp_1{{$dom}}').find('option').remove();
            $('#temp_2{{$dom}}').find('option').remove();
            $('#temp_3{{$dom}}').find('option').remove();
            $('#temp_4{{$dom}}').find('option').remove();
            $('#temp_5{{$dom}}').find('option').remove();

            var uom = '';
            if(data.uom){
                uom = data.uom
            }
            for (let v = data.top_value; v >= data.bottom_value; v = v - data.interval) {
                if( v == data.top_value){
                    $("#temp_1{{$dom}}").append($("<option></option>").attr("value", '> ' + v + data.uom).text('> ' + v + uom));
                    $("#temp_2{{$dom}}").append($("<option></option>").attr("value", '> ' + v + uom).text('> ' + v + uom));
                    $("#temp_3{{$dom}}").append($("<option></option>").attr("value", '> ' + v + uom).text('> ' + v + uom));
                    $("#temp_4{{$dom}}").append($("<option></option>").attr("value", '> ' + v + uom).text('> ' + v + uom));
                    $("#temp_5{{$dom}}").append($("<option></option>").attr("value", '> ' + v + uom).text('> ' + v + uom));
                }
                $("#temp_1{{$dom}}").append($("<option></option>").attr("value", v + uom).text(v + uom));
                $("#temp_2{{$dom}}").append($("<option></option>").attr("value", v + uom).text(v + uom));
                $("#temp_3{{$dom}}").append($("<option></option>").attr("value", v + uom).text(v + uom));
                $("#temp_4{{$dom}}").append($("<option></option>").attr("value", v + uom).text(v + uom));
                $("#temp_5{{$dom}}").append($("<option></option>").attr("value", v + uom).text(v + uom));
                if( v == data.bottom_value){
                    $("#temp_1{{$dom}}").append($("<option></option>").attr("value", '< ' + v + uom).text('< ' + v + uom));
                    $("#temp_2{{$dom}}").append($("<option></option>").attr("value", '< ' + v + uom).text('< ' + v + uom));
                    $("#temp_3{{$dom}}").append($("<option></option>").attr("value", '< ' + v + uom).text('< ' + v + uom));
                    $("#temp_4{{$dom}}").append($("<option></option>").attr("value", '< ' + v + uom).text('< ' + v + uom));
                    $("#temp_5{{$dom}}").append($("<option></option>").attr("value", '< ' + v + uom).text('< ' + v + uom));
                }
            }

            $("#temp_1{{$dom}}").val(data.temp_1);
            $("#temp_2{{$dom}}").val(data.temp_2);
            $("#temp_3{{$dom}}").val(data.temp_3);
            $("#temp_4{{$dom}}").val(data.temp_4);
            $("#temp_5{{$dom}}").val(data.temp_5);

            $("#pickerdatedate{{$dom}}").prop("disabled", true);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'temp_1': $("#temp_1{{$dom}}").val(),
                'temp_2': $("#temp_2{{$dom}}").val(),
                'temp_3': $("#temp_3{{$dom}}").val(),
                'temp_4': $("#temp_4{{$dom}}").val(),
                'temp_5': $("#temp_5{{$dom}}").val(),
                'note': $("#note{{$dom}}").val(),
                'id': {{$dom}}.data.id
            }
        },
        save: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalmanage{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        }
    }
}

</script>
