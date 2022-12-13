<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="PDF" icon="bx bxs-file-pdf" :onclick="$dom. '.event.pdf()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.pdf()" ><i class="bx bxs-file-pdf mr-50"></i>{{ __('PDF') }}</a>
                            </div>
                        </div>
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
                                        <x-pickerdate :dom="$dom" compid="fdate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Shift">
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
                                <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.show()">
                                    <span>{{ __('Show') }}</span>
                                </button>
                            </div>
                        </x-dropdown-filter>
                    </x-row-tools>
                </x-slot>
            </x-tools>

            {{-- frame --}}
            <div class="framereport" id="frame{{$dom}}">

            </div>
            {{-- frame --}}

        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalexport" title="Export Report">
    <x-form-horizontal>
        <x-row-horizontal label="Export Type">
            <input type="text" class="form-control form-control-sm" id="export_type{{$dom}}" disabled>
        </x-row-horizontal>
        <x-row-horizontal label="Plant">
            <x-select :dom="$dom" compid="eplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" :default="[$first_plant_id, $first_plant_name]"/>
        </x-row-horizontal>
        <x-row-horizontal label="Date">
            <x-pickerdate :dom="$dom" compid="edate" clear="false"/>
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.export()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->
<script>

$( document ).ready(function() {
    {{$dom}}.func.show();
});

{{$dom}} = {
   data: {
        export: '',
    },
    url: {
        report: "report/logbook/cleaning-duties/report",
        export: "report/logbook/cleaning-duties/export",
    },
    event: {
        excel: function () {
            {{$dom}}.data.export = 'EXCEL';
            {{$dom}}.func.reset();
            showModal('modalexport{{$dom}}');
        },
        pdf: function () {
            {{$dom}}.data.export = 'PDF';
            {{$dom}}.func.reset();
            showModal('modalexport{{$dom}}');
        },
    },
    func: {
        show: function () {
            loading('start', '{{ __("Generate Report") }}', 'process');
            var url ={{$dom}}.url.report + '?plant-id=' + fslctfplant{{$dom}}.get() +
                    '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd') +
                    '&section=' + $("#fsection{{$dom}}").val();

            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                dropdown.hide('hfiltertabledata{{$dom}}');
                loading("stop");
            });
        },
        reset: function () {
            $("#export_type{{$dom}}").val({{$dom}}.data.export);
            pickerdateedate{{$dom}}.set('select', "{{ date('Y/m/d') }}", { format: 'yyyy-mm-dd' });
        },
        getDataForm: function () {
            return {
                'plant': fslcteplant{{$dom}}.get(),
                'date': pickerdateedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'type': {{$dom}}.data.export
            }
        },
        export: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.export;

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    hideModal('modalexport{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        }
    }
}

</script>
