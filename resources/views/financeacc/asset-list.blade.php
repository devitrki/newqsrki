<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Sync SAP" icon="bx bx-sync" :onclick="$dom. '.event.sync()'" />
                    </x-row-tools>
                    @can('c'.$menu_id)
                    @if($mutation)
                    @unlessrole('store manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Transfer" icon="bx bx-right-top-arrow-circle" :onclick="$dom. '.event.mutation()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Cancel Transfer" icon="bx bx-x" :onclick="$dom. '.event.cancel()'" />
                    </x-row-tools>
                    @endunlessrole
                    {{-- @hasanyrole('superadmin|store manager')
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Print Delivery Document" icon="bx bx-printer" :onclick="$dom. '.event.preview()'" />
                    </x-row-tools>
                    @endhasanyrole --}}
                    @endif
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.sync()" ><i class="bx bx-sync mr-50"></i>{{ __('Sync SAP') }}</a>
                                @can('c'.$menu_id)
                                @if($mutation)
                                @unlessrole('store manager')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.mutation()" ><i class="bx bx-right-top-arrow-circle mr-50"></i>{{ __('Transfer') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.cancel()" ><i class="bx bx-x mr-50"></i>{{ __('Cancel Transfer') }}</a>
                                @endunlessrole
                                @endif
                                @endcan
                                @hasanyrole('superadmin|store manager')
                                {{-- <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.preview()" ><i class="bx bx-printer mr-50"></i>{{ __('Print Delivery Document') }}</a> --}}
                                @endhasanyrole
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
                                        @role('store manager')
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]" />
                                        @else
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]" />
                                        @endrole
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
                [
                    [
                        'label' => 'Status Transfer',
                        'data' => 'status_mutation_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'plant',
                        'data' => 'plant',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'asset number',
                        'data' => 'number',
                        'searchable' => 'true',
                        'orderable' => 'true',
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
                        'label' => 'cost center',
                        'data' => 'cost_center_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'remark',
                        'data' => 'remark',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]
                ];

                $url = 'financeacc/asset/list/dtble?plant_id=' . $first_plant_id . '&cost_center_code=' . $first_cost_center_code;
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" :url="$url" :select="[true, 'single']" :order="[2, 'asc']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Asset Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="plant{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center">
                    <input type="text" class="form-control form-control-sm" id="cost_center{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="spec_user{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="number{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sub Number">
                    <input type="text" class="form-control form-control-sm" id="sub_number{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <input type="text" class="form-control form-control-sm" id="description{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Web">
                    <input type="text" class="form-control form-control-sm" id="qty_web{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Uom">
                    <input type="text" class="form-control form-control-sm" id="uom{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Receiver">
                    <x-select :dom="$dom" compid="plant_receiver" type="serverside" url="master/plant/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <x-select :dom="$dom" compid="cost_center_receiver" type="array" size="sm" />
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="number" step=".001" class="form-control form-control-sm" id="qty_mutation{{$dom}}" min="0.001" max="1000000">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Est Transfer Date">
                    <x-pickerdate :dom="$dom" compid="est_send_date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Validator">
                    <x-select :dom="$dom" compid="validator" type="serverside" url="financeacc/asset/validator/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="note_request{{$dom}}" rows="2"></textarea>
                </x-row-horizontal>
            </div>
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

<x-modal :dom="$dom" compid="modalsync" title="Asset Sync">
    <x-form-horizontal>
        <x-row-horizontal label="Plant">
            @role('store manager')
            <x-select :dom="$dom" compid="splant" type="serverside" url="master/plant/select?auth=true" size="sm"/>
            @else
            <x-select :dom="$dom" compid="splant" type="serverside" url="master/plant/select" size="sm"/>
            @endrole
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.sync()">
            <span>{{ __('Sync') }}</span>
        </button>
    </x-slot>
</x-modal>

{{-- modal request asset Transfer --}}
<x-modal :dom="$dom" compid="modalrequest" title="Request Asset Transfer" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Asset Description')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Sender">
                    <input type="text" class="form-control form-control-sm" id="rplant{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center">
                    <input type="text" class="form-control form-control-sm" id="rcost_center{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Spec / User">
                    <input type="text" class="form-control form-control-sm" id="rspec_user{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Asset Number">
                    <input type="text" class="form-control form-control-sm" id="rnumber{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Sub Number">
                    <input type="text" class="form-control form-control-sm" id="rsub_number{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Description">
                    <input type="text" class="form-control form-control-sm" id="rdescription{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Qty Web">
                    <input type="text" class="form-control form-control-sm" id="rqty_web{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Uom">
                    <input type="text" class="form-control form-control-sm" id="ruom{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Asset Transfer')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant Receiver">
                    <x-select :dom="$dom" compid="rplant_receiver" type="serverside" url="master/plant/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Cost Center Receiver">
                    <x-select :dom="$dom" compid="rcost_center_receiver" type="array" size="sm" />
                </x-row-horizontal>
                <x-row-horizontal label="Qty Transfer">
                    <input type="number" step=".001" class="form-control form-control-sm" id="rqty_mutation{{$dom}}" min="0.001" max="1000000">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Validator">
                    <x-select :dom="$dom" compid="rvalidator" type="serverside" url="financeacc/asset/validator/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Note Request">
                    <textarea class="form-control form-control-sm" id="rnote_request{{$dom}}" rows="5"></textarea>
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveRequest()">
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

$('#select2plant_receiver{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;

    var row_data = fdtbletabledata{{$dom}}.getSelectedData();
    dataRow = row_data[0];

    url = 'financeacc/asset/costcenter/' + data.id

    if( dataRow.cost_center_code != '' ){
        url = 'financeacc/asset/costcenter/' + data.id + '?cc_code=' + dataRow.cost_center_code;
    }

    $.get( url, function (res) {
        fslctcost_center_receiver{{$dom}}.initWithData(res);
    });
});

$('#select2rplant_receiver{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    $.get( 'financeacc/asset/costcenter/' + data.id, function (res) {
        fslctrcost_center_receiver{{$dom}}.initWithData(res);
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
        save: "financeacc/asset/list",
        saveRequest: "financeacc/asset/list/request",
    },
    event: {
        sync: function () {
            showModal('modalsync{{$dom}}');
        },
        requestMutation: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check qty zero
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.qty_web < 1){
                message.info(" {{ __('Qty of this asset is zero, cannot be transferred.') }} ");
                return false;
            }

            if(data.status_request == 0){
                message.info(" {{ __('This asset is in the process of being requested, cannot be requested.') }} ");
                return false;
            }

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(check){
                    message.info(" {{ __('This asset is in the process of being transferred, cannot be requested.') }} ");
                    return false;
                }

                {{$dom}}.func.resetRequest();
                {{$dom}}.func.setRequest();
                showModal('modalrequest{{$dom}}');

            }, 'json');
        },
        mutation: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check qty zero
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.qty_web < 1){
                message.info(" {{ __('Qty of this asset is zero, cannot be transferred.') }} ");
                return false;
            }

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(check){
                    message.info(" {{ __('This asset is in the process of being transferred, cannot be transfer.') }} ");
                    return false;
                }

                {{$dom}}.func.reset();
                {{$dom}}.func.set();
                showModal('modalmanage{{$dom}}');

            }, 'json');

        },
        cancel: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.status_mutation == 0 ){
                message.info("This request not yet transfer, cannot be canceled");
                return false;
            }

            if( data.status_mutation != 1 ){
                message.info("This request have already approved, cannot be canceled");
                return false;
            }

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(!check){
                    message.info(" {{ __('This asset has not been transferred, cannot be canceled.') }} ");
                    return false;
                }

                message.confirm("Are you sure ?",
                            "Data that has been canceled cannot be restored.",
                            "{{$dom}}.func.cancel");

            }, 'json');

        },
        cancelRequestMutation: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/check/request';

            $.post( url, data, function (res) {
                var check = false;
                if( res.status == 'success' ){
                    check = res.data.check;
                }

                if(!check){
                    message.info(" {{ __('This asset has not been requested, cannot be canceled.') }} ");
                    return false;
                }

                message.confirm("Are you sure ?",
                            "Data that has been canceled cannot be restored.",
                            "{{$dom}}.func.cancelRequestMutation");

            }, 'json');

        },
        preview: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            // check
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if(data.status_mutation <= 0){
                message.info(" {{ __('Cannot preview asset transfer. This asset not yet transfer.') }} ");
                return false;
            }

            if(data.status_mutation < 5){
                message.info(" {{ __('Cannot preview asset transfer. This transfer not yet approved AM receive.') }} ");
                return false;
            }

            window.open('financeacc/asset/mutation/preview?number=' + data.number + '&sub=' + data.number_sub + '&plant_id=' + data.plant_id);

        }
    },
    func: {
        sync: function () {
            hideErrors();
            loadingModal('start');

            var url = {{$dom}}.url.save + '/sync';
            var data = {
                'plant': fslctsplant{{$dom}}.get()
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalsync{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        filter: function () {
            var url = {{$dom}}.url.save + '/dtble?plant_id=' + fslctfplant{{$dom}}.get() + '&cost_center_code=' + fslctfcostcenter{{$dom}}.get();
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#sender{{$dom}}").val('');
            $("#qty_mutation{{$dom}}").val('');
            $("#remark{{$dom}}").val('');
            $("#qty_mutation{{$dom}}").prop("disabled", false);
            fslctplant_receiver{{$dom}}.clear();
            fslctcost_center_receiver{{$dom}}.clear();
            fslctvalidator{{$dom}}.clear();
            $("#note_request{{$dom}}").val('');
        },
        resetRequest: function () {
            {{$dom}}.data.id = 0;
            fslctrvalidator{{$dom}}.clear();
            $("#rnote_request{{$dom}}").val('');
            $("#rqty_mutation{{$dom}}").val('');
            $("#rqty_mutation{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#plant{{$dom}}").val(data.plant);
            $("#cost_center{{$dom}}").val(data.cost_center_desc);
            $("#spec_user{{$dom}}").val(data.spec_user);
            $("#remark{{$dom}}").val(data.remark);
            $("#number{{$dom}}").val(data.number);
            $("#sub_number{{$dom}}").val(data.number_sub);
            $("#description{{$dom}}").val(data.description);
            $("#qty_web{{$dom}}").val(data.qty_web);

            if( data.qty_web <= 1 ){
                $("#qty_mutation{{$dom}}").val(data.qty_web);
                $("#qty_mutation{{$dom}}").prop("disabled", true);
            }

            plantId = fslctplant_receiver{{$dom}}.get();
            ccCode = fslctcost_center_receiver{{$dom}}.get();
            if (ccCode == '' || ccCode == null) {
                $.get( 'financeacc/asset/costcenter/' + plantId, function (res) {
                    fslctcost_center_receiver{{$dom}}.initWithData(res);
                });
            }
        },
        setRequest: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            $("#rplant{{$dom}}").val(data.plant);
            $("#rcost_center{{$dom}}").val(data.cost_center_desc);
            $("#rspec_user{{$dom}}").val(data.spec_user);
            $("#rremark{{$dom}}").val(data.remark);
            $("#rnumber{{$dom}}").val(data.number);
            $("#rsub_number{{$dom}}").val(data.number_sub);
            $("#rdescription{{$dom}}").val(data.description);
            $("#rqty_web{{$dom}}").val(data.qty_web);

            if( data.qty_web <= 1 ){
                $("#rqty_mutation{{$dom}}").val(data.qty_web);
                $("#rqty_mutation{{$dom}}").prop("disabled", true);
            }

            plantId = fslctrplant_receiver{{$dom}}.get();
            ccCode = fslctrcost_center_receiver{{$dom}}.get();
            if (ccCode == '' || ccCode == null) {
                $.get( 'financeacc/asset/costcenter/' + plantId, function (res) {
                    fslctrcost_center_receiver{{$dom}}.initWithData(res);
                });
            }
        },
        getDataForm: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var costcenter = $("#select2cost_center_receiver{{$dom}}").select2('data');
            var idCostCenter = "";
            var textCostCenter = "";
            if(Array.isArray(costcenter)){
                if(costcenter.length > 0){
                    idCostCenter = costcenter[0].id;
                    textCostCenter = costcenter[0].text;
                }
            }

            return {
                'plant_id': data.plant_id,
                'number': data.number,
                'number_sub': data.number_sub,
                'cost_center': data.cost_center,
                'cost_center_code': data.cost_center_code,
                'qty_mutation': $("#qty_mutation{{$dom}}").val(),
                'plant_receiver': fslctplant_receiver{{$dom}}.get(),
                'plant_sender': data.plant_id,
                'cost_center_receiver': textCostCenter,
                'cost_center_code_receiver': idCostCenter,
                'remark': $("#remark{{$dom}}").val(),
                'sender': $("#sender{{$dom}}").val(),
                'est_send_date': pickerdateest_send_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'note_request': $("#note_request{{$dom}}").val(),
                'validator': fslctvalidator{{$dom}}.get(),
            }
        },
        getDataFormRequest: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var costcenter = $("#select2rcost_center_receiver{{$dom}}").select2('data');
            var idCostCenter = "";
            var textCostCenter = "";
            if(Array.isArray(costcenter)){
                if(costcenter.length > 0){
                    idCostCenter = costcenter[0].id;
                    textCostCenter = costcenter[0].text;
                }
            }

            return {
                'plant_id': data.plant_id,
                'number': data.number,
                'number_sub': data.number_sub,
                'cost_center': data.cost_center,
                'qty_mutation': $("#rqty_mutation{{$dom}}").val(),
                'plant_receiver': fslctrplant_receiver{{$dom}}.get(),
                'validator': fslctrvalidator{{$dom}}.get(),
                'plant_sender': data.plant_id,
                'plant_sender_code': data.plant_code,
                'cost_center_receiver': textCostCenter,
                'cost_center_code_receiver': idCostCenter,
                'note_request': $("#rnote_request{{$dom}}").val(),
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
        },
        saveRequest: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormRequest();

            if( data.plant_sender_code.charAt(0) != 'R' ){
                loadingModal("stop");
                message.info("{{ __('Plant can request transfer only from DC.') }}");
                return false;
            }

            var url = {{$dom}}.url.saveRequest;
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalrequest{{$dom}}');
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        cancel: function () {
            loading('start', '{{ __("Cancel Asset Transfer") }}', 'process');

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/cancel';

            $.post( url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
        cancelRequestMutation: function () {
            loading('start', '{{ __("Cancel Request Asset Transfer") }}', 'process');

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            var data = {
                'number' : data.number,
                'number_sub' : data.number_sub,
                'plant_id' : data.plant_id,
            };

            var url = {{$dom}}.url.save + '/cancel/request';

            $.post( url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        }
    }
}

</script>
