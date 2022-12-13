<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Excel" icon="bx bx-file" :onclick="$dom. '.event.excel()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.excel()" ><i class="bx bx-file mr-50"></i>{{ __('Excel') }}</a>
                            </div>
                        </div>
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
{{$dom}} = {
   data: {
        export: '',
    },
    url: {
        export: "report/interfaces/payment-detail-vtec/export",
    },
    event: {
        excel: function () {
            {{$dom}}.data.export = 'EXCEL';
            {{$dom}}.func.reset();
            showModal('modalexport{{$dom}}');
        },
    },
    func: {
        reset: function () {
            $("#export_type{{$dom}}").val({{$dom}}.data.export);
            pickerdateedate{{$dom}}.set('select', "{{ date('Y/m/d', strtotime('-1 days')) }}", { format: 'yyyy-mm-dd' });
        },
        getDataForm: function () {
            return {
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
