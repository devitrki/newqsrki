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
                        'label' => 'gr plant (in)',
                        'data' => 'stock_in_gr_plant_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'dc (in)',
                        'data' => 'stock_in_dc_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'vendor (in)',
                        'data' => 'stock_in_vendor_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'section (in)',
                        'data' => 'stock_in_section_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'gi plant (out)',
                        'data' => 'stock_out_gi_plant_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'dc (out)',
                        'data' => 'stock_out_dc_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'vendor (out)',
                        'data' => 'stock_out_vendor_input',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'class' => 'input'
                    ],[
                        'label' => 'section (out)',
                        'data' => 'stock_out_section_input',
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
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/daily-inventory/warehouse/dtble?plant-id={{$first_plant_id}}&date={{ date('Y/m/d') }}" />
        </div>
    </div>
</x-card-scroll>

<script>
function changeLbDlyInvWarehouse(id, field) {
    var value = '';

    value = $("#lbdlyinvwh" + field + id).val();

    var data = {
        'field': field,
        'value': value,
        'id': id,
    };

    var url = {{$dom}}.url.save + '/update';

    $.post(url, data, function (res) {
        toastr.remove();
        if (res.status == 'success') {

            // stockOpening = parseFloat($("#lbdlyinvwhstock_opening" + id).val());

            // stockInGR = parseFloat($("#lbdlyinvwhstock_in_gr_plant" + id).val());
            // stockInDC = parseFloat( $("#lbdlyinvwhstock_in_dc" + id).val() );
            // stockInVendor = parseFloat( $("#lbdlyinvwhstock_in_vendor" + id).val() );
            // stockInSection = parseFloat( $("#lbdlyinvwhstock_in_section" + id).val() );

            // stockOutGI = parseFloat( $("#lbdlyinvwhstock_out_gi_plant" + id).val() );
            // stockOutDC = parseFloat( $("#lbdlyinvwhstock_out_dc" + id).val() );
            // stockOutVendor = parseFloat( $("#lbdlyinvwhstock_out_vendor" + id).val() );
            // stockOutSection = parseFloat( $("#lbdlyinvwhstock_out_section" + id).val() );

            // stockIn = stockInGR + stockInDC + stockInVendor + stockInSection;
            // stockOut = stockOutGI + stockOutDC + stockOutVendor + stockOutSection;

            // // calculation stock closing
            // stockClosing = stockOpening + stockIn - stockOut;

            // if( stockClosing % 1 != 0 ){
            //     stockClosing = stockClosing.toFixed(2);
            // }

            // $("#lbdlyinvwhstock_closing" + id).val(stockClosing);

            toastr.success(res.message, 'Update Daily Inventory Warehouse');
        } else {
            toastr.error(res.message, 'Update Daily Inventory Warehouse');
        }
    }, 'json');

}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "logbook/daily-inventory/warehouse",
        datatable: "logbook/daily-inventory/warehouse/dtble"
    },
    event: {
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + $("#select2fplant{{$dom}}").val() + '&date=' + pickerdatefdate{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        }
    }
}

</script>
