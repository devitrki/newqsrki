<x-form-vertical :error="false">
    <div class="row mx-0">
        <div class="col-12 text-center mt-1">
            <h5>Product <b>{{ $product }}</b> ( <b>{{ App\Library\Helper::DateConvertFormat($date, 'Y/m/d', 'd-m-Y') }}</b> )</h5>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="PRODUCTION PLANNING" />
        </div>
        <div class="col-12 mb-1">
            <x-row-vertical label="Pick Time">
                <x-select :dom="$dom" compid="pt_time" type="array" :options="$times" size="sm"/>
            </x-row-vertical>
        </div>
        <div class="col-12 col-sm-6">
            <x-row-horizontal label="Plan Cooking">
                <input type="number" class="form-control form-control-sm" id="lbptplan_cooking{{$dom}}" value="{{ $lbProdTime->plan_cooking }}" onchange="changelbprodtime({{ $lbProdTime->id }}, 'plan_cooking')">
            </x-row-horizontal>
            <x-row-horizontal label="Plan Total Cooking">
                <input type="number" class="form-control form-control-sm" id="lbptplan_cooking_total{{$dom}}" value="{{ $lbProdTime->plan_cooking_total }}" onchange="changelbprodtime({{ $lbProdTime->id }}, 'plan_cooking_total')">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-sm-6">
            <x-row-horizontal label="Actual Cooking">
                <input type="number" class="form-control form-control-sm" id="lbptact_cooking{{$dom}}" value="{{ $lbProdTime->act_cooking }}" onchange="changelbprodtime({{ $lbProdTime->id }}, 'act_cooking')">
            </x-row-horizontal>
            <x-row-horizontal label="Actual Total Cooking">
                <input type="number" class="form-control form-control-sm" id="lbptact_cooking_total{{$dom}}" value="{{ $lbProdTime->act_cooking_total }}" onchange="changelbprodtime({{ $lbProdTime->id }}, 'act_cooking_total')">
            </x-row-horizontal>
        </div>
        <div class="col-12 mb-1">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletablept'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                        [
                            [
                                'label' => 'quantity',
                                'data' => 'quantity_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'exp & prod code',
                                'data' => 'exp_prod_code_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'fryer',
                                'data' => 'fryer_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'temperature',
                                'data' => 'temperature_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'holding time',
                                'data' => 'holding_time_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'self life',
                                'data' => 'self_life_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'vendor',
                                'data' => 'vendor_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                        ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tablept" :columns="$columns" url="logbook/production-planning/prodtime/dtble?prod-plan-id={{ $lbProdPlan->id }}&time=6:00" footer="false" height="146" number="false"/>
        </div>
        <div class="col-12">
            <x-row-vertical label="Total Usage">
                <input type="number" class="form-control form-control-sm" id="lbpttotal_usage{{$dom}}" value="{{ $lbProdPlan->total_usage }}" onchange="changelbprodplan({{ $lbProdPlan->id }}, 'total_usage')">
            </x-row-vertical>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="CHICKEN INTERNAL TEMPERATURE" />
        </div>
        <div class="col-12 mb-2">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createProdTemp()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom . '.event.editProdTemp()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deleteProdTemp()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletableprodtemp'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = [[
                                'label' => 'food name',
                                'data' => 'food_name',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'time',
                                'data' => 'time',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'fryer temperature',
                                'data' => 'fryer_temp',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'product temperature',
                                'data' => 'product_temp',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Product Status',
                                'data' => 'result',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'corrective action',
                                'data' => 'corrective_action',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'pic',
                                'data' => 'pic',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tableprodtemp" :columns="$columns" url="logbook/production-planning/prodtemp/dtble?prod-plan-id={{ $lbProdPlan->id }}" :select="[true, 'single']" footer="false" height="160"/>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="FRYER TEMPERATURE VERIFICATION" />
        </div>
        <div class="col-12 mb-1">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabletempverify'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                        [
                            [
                                'label' => 'fryer',
                                'data' => 'fryer',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'shift 1',
                                'data' => 'shift1_temp_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'shift 2',
                                'data' => 'shift2_temp_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'shift 3',
                                'data' => 'shift3_temp_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'note',
                                'data' => 'note_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ]
                        ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabletempverify" :columns="$columns" url="logbook/production-planning/prodtempverify/dtble?prod-plan-id={{ $lbProdPlan->id }}" footer="false" height="146" number="false"/>
            <small>Note : {{ __('This process is carried out using a thermometer') }}</small>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="OIL QUALITY CHECKING WITH VITO" />
        </div>
        <div class="col-12 mb-1">
            <x-row-vertical label="TIME">
                <input type="text" class="form-control form-control-sm" id="lbpttime_check_quality{{$dom}}" value="{{ $lbProdPlan->time_check_quality }}" onchange="changelbprodplan({{ $lbProdPlan->id }}, 'time_check_quality')">
            </x-row-vertical>
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletablequality'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                        [
                            [
                                'label' => 'fryer',
                                'data' => 'fryer',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'TMP (%)',
                                'data' => 'tpm_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'Temp (C)',
                                'data' => 'temp_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'Change = X, Refill = L /lbs',
                                'data' => 'oil_status_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'filtration',
                                'data' => 'filtration_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ]
                        ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tablequality" :columns="$columns" url="logbook/production-planning/prodquality/dtble?prod-plan-id={{ $lbProdPlan->id }}" footer="false" height="146" number="false"/>
            <small>Note : {{ __('For outlets that are still using the test oil kit, stick it on the tpm column') }}</small>
        </div>

        <div class="col-12 mt-2">
            <x-divider-text text="USED OIL" />
        </div>
        <div class="col-12 mb-3">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletableusedoil'.$dom.'.refresh()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns =
                        [
                            [
                                'label' => 'stock first',
                                'data' => 'stock_first_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock in gr',
                                'data' => 'stock_in_gr_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock in fryer a',
                                'data' => 'stock_in_fryer_a_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock in fryer b',
                                'data' => 'stock_in_fryer_b_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock in fryer c',
                                'data' => 'stock_in_fryer_c_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock in fryer d',
                                'data' => 'stock_in_fryer_d_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock change oil',
                                'data' => 'stock_change_oil_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock out',
                                'data' => 'stock_out_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'stock last',
                                'data' => 'stock_last_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'note',
                                'data' => 'note_input',
                                'searchable' => 'false',
                                'orderable' => 'false',
                                'class' => 'input'
                            ],
                        ];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tableusedoil" :columns="$columns" url="logbook/production-planning/produsedoil/dtble?prod-plan-id={{ $lbProdPlan->id }}" footer="false" height="146" number="false"/>
            <small>Note : {{ __('The initial stock for the oil in the fryer is not counted') }}</small>
        </div>

    </div>
</x-form-vertical>

<!-- modal -->
<x-modal :dom="$dom" compid="modalprodtemp" title="Chicken Internal Temperature">
    <x-form-horizontal>
        <x-row-horizontal label="Food Name">
            <input type="text" class="form-control form-control-sm" id="pte_food_name{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Time">
            <input type="text" class="form-control form-control-sm" id="pte_time{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Fryer Temperature">
            <input type="number" class="form-control form-control-sm" id="pte_fryer_temp{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Product Temperature">
            <input type="number" class="form-control form-control-sm" id="pte_product_temp{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Product Status">
            @php
                $options = [
                            ['id' => 'OK', 'text' => 'OK'],
                            ['id' => 'Not OK', 'text' => 'Not OK'],
                        ];
            @endphp
            <x-select :dom="$dom" compid="pte_result" type="array" :options="$options" size="sm"/>
        </x-row-horizontal>
        <x-row-horizontal label="Corrective Action">
            <input type="text" class="form-control form-control-sm" id="pte_corrective_action{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="PIC">
            <input type="text" class="form-control form-control-sm" id="pte_pic{{$dom}}">
        </x-row-horizontal>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveProdTemp()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>
$(document).ready(function() {
    $("#pte_time{{$dom}}").inputmask({
        mask: "99:99"
    });
    $("#lbpttime_check_quality{{$dom}}").inputmask({
        mask: "99:99"
    });
});

// change prod time
$('#select2pt_time{{$dom}}').on('select2:select', function (e) {
    var time = fslctpt_time{{$dom}}.get();

    urlDtble = {{$dom}}.url.prodtime + '/dtble?prod-plan-id={{ $lbProdPlan->id }}&time=' + time;
    urlInput = {{$dom}}.url.prodtime + '/detail?prod-plan-id={{ $lbProdPlan->id }}&time=' + time;

    $.get( urlInput, function (res) {
        $("#lbptplan_cooking{{$dom}}").val(res.data.plan_cooking);
        $("#lbptplan_cooking_total{{$dom}}").val(res.data.plan_cooking_total);
        $("#lbptact_cooking{{$dom}}").val(res.data.act_cooking);
        $("#lbptact_cooking_total{{$dom}}").val(res.data.act_cooking_total);
    });

    fdtbletablept{{$dom}}.changeUrl(urlDtble);
});

// change for prod plan
function changelbprodplan(id, field) {
    data = $("#lbpt" + field + "{{$dom}}").val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.prodplan + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

// change for prod time
function changelbprodtime(id, field) {
    data = $("#lbpt" + field + "{{$dom}}").val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.prodtime + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

// change for prod time detail
function changelbprodtimedetail(id, field) {
    var data = "";
    data = $("#lbprodtime" + field + id).val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.prodtime + '/detail/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

// change for prod temp verify
function changelbprodtempverify(id, field) {
    data = $("#lbprodtempverify" + field + id).val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.prodtempverify + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

// change for prod quality
function changelbprodquality(id, field) {
    data = $("#lbprodquality" + field + id).val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.prodquality + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

// change for prod used oil
function changelbprodusedoil(id, field) {
    data = $("#lbprodusedoil" + field + id).val();

    var data = {
        'field': field,
        'data': data,
        'id': id,
    };

    var url = {{$dom}}.url.produsedoil + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {
            toastr.success(res.message, 'Update Production Planning');
        } else {
            toastr.error(res.message, 'Update Production Planning');
        }
    }, 'json');

}

{{$dom}} = {
    data: {
        idProdTemp: 0,
    },
    url: {
        save: "logbook/production-planning",
        prodplan : "logbook/production-planning/prodplan",
        prodtime : "logbook/production-planning/prodtime",
        prodtemp : "logbook/production-planning/prodtemp",
        prodtempverify : "logbook/production-planning/prodtempverify",
        prodquality : "logbook/production-planning/prodquality",
        produsedoil : "logbook/production-planning/produsedoil",
    },
    event: {
        createProdTemp: function () {
            {{$dom}}.func.resetProdTemp();
            showModal('modalprodtemp{{$dom}}');
        },
        editProdTemp: function () {
            var rows = fdtbletableprodtemp{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.resetProdTemp();
            {{$dom}}.func.setProdTemp();
        },
        deleteProdTemp: function () {
            var rows = fdtbletableprodtemp{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.deleteProdTemp");
        },
    },
    func: {
        resetProdTemp: function () {
            {{$dom}}.data.idProdTemp = 0;
            $("#pte_food_name{{$dom}}").val('');
            $("#pte_time{{$dom}}").val('');
            $("#pte_fryer_temp{{$dom}}").val('');
            $("#pte_product_temp{{$dom}}").val('');
            fslctpte_result{{$dom}}.clear();
            $("#pte_corrective_action{{$dom}}").val('');
            $("#pte_pic{{$dom}}").val('');
        },
        setProdTemp: function () {
            var row_data = fdtbletableprodtemp{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.idProdTemp = data.id;

            $("#pte_food_name{{$dom}}").val(data.food_name);
            $("#pte_time{{$dom}}").val(data.time);
            $("#pte_fryer_temp{{$dom}}").val(data.fryer_temp);
            $("#pte_product_temp{{$dom}}").val(data.product_temp);
            fslctpte_result{{$dom}}.set(data.result, data.result);
            $("#pte_corrective_action{{$dom}}").val(data.corrective_action);
            $("#pte_pic{{$dom}}").val(data.pic);

            showModal('modalprodtemp{{$dom}}');
        },
        getDataFormProdTemp: function () {
            return {
                'food_name': $("#pte_food_name{{$dom}}").val(),
                'time': $("#pte_time{{$dom}}").val(),
                'fryer_temperature': $("#pte_fryer_temp{{$dom}}").val(),
                'product_temperature': $("#pte_product_temp{{$dom}}").val(),
                'product_status': fslctpte_result{{$dom}}.get(),
                'corrective_action': $("#pte_corrective_action{{$dom}}").val(),
                'pic': $("#pte_pic{{$dom}}").val(),
                'lb_prod_plan_id': {{ $lbProdPlan->id }},
                'id': {{$dom}}.data.id
            }
        },
        saveProdTemp: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormProdTemp();
            var url = {{$dom}}.url.prodtemp;
            if( {{$dom}}.data.idProdTemp != 0 ){
                url += '/' + {{$dom}}.data.idProdTemp;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletableprodtemp{{$dom}}.refresh();
                    hideModal('modalprodtemp{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        deleteProdTemp: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletableprodtemp{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.prodtemp + '/' + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletableprodtemp{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
    }
}
</script>
