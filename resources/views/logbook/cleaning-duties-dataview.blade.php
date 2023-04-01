<x-form-vertical :error="false">
    <div class="row mx-0">
        <div class="col-12 text-center mt-1">
            <h5>{{ $section }} Section ( {{ App\Library\Helper::DateConvertFormat($date, 'Y/m/d', 'd-m-Y') }} )</h5>
        </div>
        <div class="col-12">
            <x-divider-text text="DAILY TASK" />
        </div>
        <div class="col-12">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledaily'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = [[
                                'label' => 'task',
                                'data' => 'task',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Opening',
                                'data' => 'opening_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ],
                            [
                                'label' => 'Closing',
                                'data' => 'closing_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ],
                            [
                                'label' => 'Midnite',
                                'data' => 'midnite_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledaily" :columns="$columns" url="logbook/operational/cleaning-duties/daily/dtble?clean-duties-id={{ $lbCleanDuties->id }}" footer="false" height="300" number="false"/>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="WEEKLY TASK" />
        </div>
        <div class="col-12">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletableweekly'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = [[
                                'label' => 'task',
                                'data' => 'task',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'day',
                                'data' => 'day',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Opening',
                                'data' => 'opening_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ],
                            [
                                'label' => 'Closing',
                                'data' => 'closing_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ],
                            [
                                'label' => 'Midnite',
                                'data' => 'midnite_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input text-center',
                            ],
                            [
                                'label' => 'pic',
                                'data' => 'pic_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tableweekly" :columns="$columns" url="logbook/operational/cleaning-duties/weekly/dtble?clean-duties-id={{ $lbCleanDuties->id }}" footer="false" height="300" number="false"/>
        </div>
        <div class="col-12 mt-2">
            <x-divider-text text="NOTE" />
        </div>
        <div class="col-12">
            <x-row-vertical label="Note">
                <textarea class="form-control form-control-sm" id="note{{$dom}}" rows="10">{{ $lbCleanDuties->note }}</textarea>
            </x-row-vertical>
        </div>
        <div class="col-12 text-center mb-2">
            <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveCleaningDuties()" id="btnSaveCleaningDuties{{$dom}}">
                <span>{{ __('Save') }}</span>
            </button>
        </div>
    </div>
</x-form-vertical>

<!-- modal -->
<!-- end modal -->

<script>
function changeLbDlyCleanDuties(id, shift) {
    if( shift == 'opening' ){
        check = $("#checkdlyclndtiso" + id).is(':checked');
    } else if( shift == 'closing' ){
        check = $("#checkdlyclndtisc" + id).is(':checked');
    } else {
        check = $("#checkdlyclndtism" + id).is(':checked');
    }

    var data = {
        'shift': shift,
        'checklist': (check) ? 1 : 0,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/daily/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Cleaning Duties (Daily Task)');
        } else {
            toastr.error(res.message, 'Update Cleaning Duties (Daily Task)');
        }
    }, 'json');

}

function changeLbWlyCleanDuties(id, field) {
    var data = "";
    if( field == 'opening' ){
        check = $("#checkwlyclndtiso" + id).is(':checked');
        data = (check) ? 1 : 0;
    } else if( field == 'closing' ){
        check = $("#checkwlyclndtisc" + id).is(':checked');
        data = (check) ? 1 : 0;
    } else if( field == 'midnite' ){
        check = $("#checkwlyclndtism" + id).is(':checked');
        data = (check) ? 1 : 0;
    } else {
        data = $("#tfkwlyclndtispic" + id).val();
    }

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/weekly/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Cleaning Duties (Weekly Task)');
        } else {
            toastr.error(res.message, 'Update Cleaning Duties (Weekly Task)');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        checklist: 0,
        idWeekly: 0,
        idCleanDuties: {{ $lbCleanDuties->id }},
    },
    url: {
        save: "logbook/operational/cleaning-duties",
    },
    event: {
    },
    func: {
        saveCleaningDuties: function () {
            hideErrors();
            loading('start');

            var data = {
                'note': $("#note{{$dom}}").val(),
                'id': {{$dom}}.data.idCleanDuties
            };
            var url = {{$dom}}.url.save + '/note';
            url += '/' + {{$dom}}.data.idCleanDuties;
            data._method = 'PUT';

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        }
    }
}
</script>
