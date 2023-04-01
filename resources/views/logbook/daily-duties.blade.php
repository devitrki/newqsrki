<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools>
                        <x-button-tools tooltip="Note" icon="bx bx-note" :onclick="$dom. '.event.note()'" />
                    </x-row-tools>
                    @endcan
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
                                    <x-row-vertical label="Section">
                                        <select class="form-control form-control-sm" id="fsection{{$dom}}">
                                            @php
                                                $options = [
                                                            ['value' => 'Cashier', 'text' => 'Cashier'],
                                                            ['value' => 'Lobby', 'text' => 'Lobby'],
                                                            ['value' => 'Kitchen', 'text' => 'Kitchen']
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
                        'label' => 'section',
                        'data' => 'section',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'task',
                        'data' => 'task',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'opening',
                        'data' => 'opening_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input text-center',
                    ],[
                        'label' => 'closing',
                        'data' => 'closing_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input text-center',
                    ],[
                        'label' => 'midnite',
                        'data' => 'midnite_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input text-center',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/operational/daily-duties/dtble?plant-id={{$first_plant_id}}&date={{ date('Y/m/d') }}&section=Cashier" :select="[true, 'single']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalnote" title="Daily Duties Note">
    <x-form-vertical>
        <x-row-vertical label="Date">
            <x-pickerdate :dom="$dom" compid="datenote" data-value="{{ date('Y/m/d') }}" clear="false"/>
        </x-row-vertical>
        <x-row-vertical label="Section">
            <input type="text" class="form-control form-control-sm" id="sectionnote{{$dom}}" disabled>
        </x-row-vertical>
        <x-row-vertical label="Note">
            <textarea class="form-control form-control-sm" id="note{{$dom}}" rows="10"></textarea>
        </x-row-vertical>
    </x-form-vertical>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveNote()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->
<script>

function changeLbDlyDuties(id, shift) {
    if( shift == 'opening' ){
        check = $("#checklbdlydutieso" + id).is(':checked');
    } else if( shift == 'closing' ){
        check = $("#checklbdlydutiesc" + id).is(':checked');
    } else {
        check = $("#checklbdlydutiesm" + id).is(':checked');
    }

    var data = {
        'shift': shift,
        'checklist': (check) ? 1 : 0,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Daily Duties');
        } else {
            toastr.error(res.message, 'Update Daily Duties');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        checklist: 0,
        idDuties: 0,
    },
    url: {
        save: "logbook/operational/daily-duties",
        datatable: "logbook/operational/daily-duties/dtble"
    },
    event: {
        note: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            {{$dom}}.func.setNote();
        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd') + '&section=' + $("#fsection{{$dom}}").val();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        setNote: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.idDuties = data.duties_id;

            $("#sectionnote{{$dom}}").val(data.section);
            $("#note{{$dom}}").val(data.note);

            $("#pickerdatedatenote{{$dom}}").prop("disabled", true);
            pickerdatedatenote{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });

            showModal('modalnote{{$dom}}');
        },
        saveNote: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'note': $("#note{{$dom}}").val(),
                'id': {{$dom}}.data.idDuties
            };

            var url = {{$dom}}.url.save + '/note';
            url += '/' + {{$dom}}.data.idDuties;
            data._method = 'PUT';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalnote{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        }
    }
}

</script>
