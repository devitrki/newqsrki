<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Print Delivery Document" icon="bx bx-printer" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-printer mr-50"></i>{{ __('Print Delivery Document') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbletabledata{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
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
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Cost Center">
                                        <x-select :dom="$dom" compid="fcostcenter" type="array" size="sm" dropdowncompid="tabledata" />
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
                    <x-row-tools>
                        <x-dropdown-export :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-input-search :dom="$dom" dtblecompid="tabledata" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                    [[
                        'label' => 'type',
                        'data' => 'type',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'asset number',
                        'data' => 'number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'sub number',
                        'data' => 'number_sub',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'description',
                        'data' => 'description',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'spec / user',
                        'data' => 'spec_user',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'remark',
                        'data' => 'remark',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'qty',
                        'data' => 'qty_mutation',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'uom',
                        'data' => 'uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant sender',
                        'data' => 'plant_from',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'cost center sender',
                        'data' => 'from_cost_center_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic sender',
                        'data' => 'pic_sender',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant receiver',
                        'data' => 'plant_to',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'cost center receiver',
                        'data' => 'to_cost_center_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'requestor',
                        'data' => 'requestor_desc',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'validator',
                        'data' => 'validator',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approver 1',
                        'data' => 'approver1',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approver 2',
                        'data' => 'approver2',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'est transfer date',
                        'data' => 'date_send_est_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'request date',
                        'data' => 'date_request_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approve 1 date',
                        'data' => 'date_approve_first_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'confirm validator date',
                        'data' => 'date_confirmation_validator_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'approve 2 date',
                        'data' => 'date_approve_second_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'confirm sender date',
                        'data' => 'date_confirmation_sender_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'condition asset transfer',
                        'data' => 'condition_send',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'note request',
                        'data' => 'note_request',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="financeacc/asset/printsj/dtble?plant_id={{ $first_plant_id }}&cost_center_code={{ $first_cost_center_code }}" :select="[true, 'multiple']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Company">
    <x-form-horizontal>
        <x-row-horizontal label="Name">
            <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="sap">
            <input type="text" class="form-control form-control-sm" id="sap{{$dom}}">
        </x-row-horizontal>
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
$('#select2fplant{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctfcostcenter{{$dom}}.initWithData(res);
    });
});

$("#hbtnfiltertabledata{{$dom}}").on("click change", function(e) {
    plantId = fslctfplant{{$dom}}.get();
    ccCode = fslctfcostcenter{{$dom}}.get();

    if (ccCode == '' || ccCode == null) {
        $.get( 'financeacc/asset/costcenter/' + plantId, function (res) {
            fslctfcostcenter{{$dom}}.initWithData(res);
        });
    }
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "financeacc/asset/printsj",
    },
    event: {
        preview: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();

            console.log(row_data);

            var param = '';

            var currentPlant = 0;
            var statusPlantSame = true;

            for (let i = 0; i < row_data.length; i++) {
                if( i == 0 ){
                    // first
                    param += row_data[i].id;
                } else {
                    param = param + ',' + row_data[i].id;
                }

                if( row_data[i].to_plant_id != currentPlant && currentPlant != 0 ){
                    statusPlantSame = false;
                }

                currentPlant = row_data[i].to_plant_id;
            }

            if( !statusPlantSame ){
                message.info(" {{ __('Print SJ must have same plant receiver.') }} ");
                return false;
            }

            window.open('financeacc/asset/printsj/preview?id=' + param);

        }
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.save + '/dtble?plant_id=' + fslctfplant{{$dom}}.get() + '&cost_center_code=' + fslctfcostcenter{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        }
    }
}

</script>
