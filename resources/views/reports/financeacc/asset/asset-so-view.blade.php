<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    {{-- <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="PDF" icon="bx bxs-file-pdf" :onclick="$dom. '.event.pdf()'" />
                    </x-row-tools> --}}
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Excel" icon="bx bx-file" :onclick="$dom. '.event.excel()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.pdf()" ><i class="bx bxs-file-pdf mr-50"></i>{{ __('PDF') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.excel()" ><i class="bx bx-file mr-50"></i>{{ __('Excel') }}</a>
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
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Cost Center">
                                        <x-select :dom="$dom" compid="fcostcenter" type="array" size="sm" dropdowncompid="tabledata" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Periode">
                                        <x-select :dom="$dom" compid="fperiode" type="serverside" url="financeacc/asset/so/select/periode" size="sm"/>
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
            <x-select :dom="$dom" compid="eplant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Cost Center">
            <x-select :dom="$dom" compid="ecostcenter" type="array" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Periode">
            <x-select :dom="$dom" compid="eperiode" type="serverside" url="financeacc/asset/so/select/periode" size="sm"/>
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
$('#select2fplant{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctfcostcenter{{$dom}}.initWithData(res);
    });
});

$('#select2eplant{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctecostcenter{{$dom}}.initWithData(res);
    });
});

{{$dom}} = {
   data: {
        export: '',
    },
    url: {
        report: "report/financeacc/asset-so/report",
        export: "report/financeacc/asset-so/export",
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
            dropdown.hide('hfiltertabledata{{$dom}}');

            if(fslctfplant{{$dom}}.get() == '' || fslctfplant{{$dom}}.get() == null){
                message.info(" {{ __('Please select plant first') }} ");
                return false;
            }

            if(fslctfcostcenter{{$dom}}.get() == '' || fslctfcostcenter{{$dom}}.get() == null){
                message.info(" {{ __('Please select cost center first') }} ");
                return false;
            }

            if(fslctfperiode{{$dom}}.get() == '' || fslctfperiode{{$dom}}.get() == null){
                message.info(" {{ __('Please select periode first') }} ");
                return false;
            }

            loading('start', '{{ __("Generate Report") }}', 'process');

            var url ={{$dom}}.url.report + '?plant-id=' + fslctfplant{{$dom}}.get() +
                    '&costcenter=' + fslctfcostcenter{{$dom}}.get() +
                    '&periode=' + fslctfperiode{{$dom}}.get();

            $('#frame{{$dom}}').load( url, function (response, status, xhr) {
                loading("stop");
            });
        },
        reset: function () {
            $("#export_type{{$dom}}").val({{$dom}}.data.export);
        },
        getDataForm: function () {
            return {
                'periode': fslcteperiode{{$dom}}.get(),
                'plant': fslcteplant{{$dom}}.get(),
                'cost_center': fslctecostcenter{{$dom}}.get(),
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
