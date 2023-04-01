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
                        'label' => 'date',
                        'data' => 'date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'product name',
                        'data' => 'product_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'uom',
                        'data' => 'uom',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'frekuensi',
                        'data' => 'frekuensi',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'stock opening',
                        'data' => 'stock_opening_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'stock in',
                        'data' => 'stock_in_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'stock out',
                        'data' => 'stock_out_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'stock closing',
                        'data' => 'stock_closing_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'note',
                        'data' => 'note_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/daily-inventory/cashier/dtble?plant-id={{$first_plant_id}}&date={{ date('Y/m/d') }}" />
        </div>
    </div>
</x-card-scroll>

<script>
function changeLbDlyInvCashier(id, field) {
    var value = '';

    value = $("#lbdlyinvcshir" + field + id).val();

    var data = {
        'field': field,
        'value': value,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/update';
    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {

            // stockOpening = parseFloat($("#lbdlyinvcshirstock_opening" + id).val());
            // stockIn = parseFloat( $("#lbdlyinvcshirstock_in" + id).val() );
            // stockOut = parseFloat( $("#lbdlyinvcshirstock_out" + id).val() );

            // // calculation stock closing
            // stockClosing = stockOpening + stockIn - stockOut;

            // if( stockClosing % 1 != 0 ){
            //     stockClosing = stockClosing.toFixed(2);
            // }

            // $("#lbdlyinvcshirstock_closing" + id).val(stockClosing);

            toastr.success(res.message, 'Update Daily Inventory Cashier');
        } else {
            toastr.error(res.message, 'Update Daily Inventory Cashier');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "logbook/daily-inventory/cashier",
        datatable: "logbook/daily-inventory/cashier/dtble"
    },
    event: {
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + $("#select2fplant{{$dom}}").val() + '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#product_name{{$dom}}").val('');
            $("#uom{{$dom}}").val('');
            $("#frekuensi{{$dom}}").val('');
            $("#stock_opening{{$dom}}").val('');
            $("#stock_in{{$dom}}").val('');
            $("#stock_out{{$dom}}").val('');
            $("#stock_closing{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            $("#product_name{{$dom}}").val(data.product_name);
            $("#uom{{$dom}}").val(data.uom);
            $("#frekuensi{{$dom}}").val(data.frekuensi);
            $("#stock_opening{{$dom}}").val(data.stock_opening);
            $("#stock_in{{$dom}}").val(data.stock_in);
            $("#stock_out{{$dom}}").val(data.stock_out);
            $("#stock_closing{{$dom}}").val(data.stock_closing);
            $("#pickerdatedate{{$dom}}").prop("disabled", true);

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'product_name': $("#product_name{{$dom}}").val(),
                'uom': $("#uom{{$dom}}").val(),
                'frekuensi': $("#frekuensi{{$dom}}").val(),
                'stock_opening': $("#stock_opening{{$dom}}").val(),
                'stock_in': $("#stock_in{{$dom}}").val(),
                'stock_out': $("#stock_out{{$dom}}").val(),
                'stock_closing': $("#stock_closing{{$dom}}").val(),
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
