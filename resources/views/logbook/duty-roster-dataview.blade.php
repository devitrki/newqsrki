<x-form-vertical :error="false">
    <div class="row mx-0">
        <div class="col-12 text-center mt-1">
            <h5>{{ $section }} Shift ( {{ App\Library\Helper::DateConvertFormat($date, 'Y/m/d', 'd-m-Y') }} )</h5>
        </div>
        <div class="col-12">
            <x-divider-text text="BRIEFING" />
        </div>

        <div class="col-12 col-md-6">
            <x-row-vertical label="Sales Target">
                <input type="text" class="form-control form-control-sm" id="sales_target{{$dom }}" value="{{ $lbBreafings->sales_target }}">
            </x-row-vertical>
            <x-row-vertical label="MTD Sales">
                <input type="text" class="form-control form-control-sm" id="mtd_sales{{$dom }}" value="{{ $lbBreafings->mtd_sales }}">
            </x-row-vertical>
        </div>
        <div class="col-12 col-md-6">
            <x-row-vertical label="Today's Highlight">
                <input type="text" class="form-control form-control-sm" id="highlight{{$dom }}" value="{{ $lbBreafings->highlight }}">
            </x-row-vertical>
            <x-row-vertical label="RF Updates">
                <input type="text" class="form-control form-control-sm" id="rf_updates{{$dom }}" value="{{ $lbBreafings->rf_updates }}">
            </x-row-vertical>
        </div>
        <div class="col-12">
            <x-divider-text text="DUTY ROSTER" />
        </div>
        <div class="col-12">
            <div>
                <x-tools class="border">
                    <x-slot name="left">
                        <x-row-tools>
                            <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                        </x-row-tools>
                    </x-slot>
                </x-tools>
            </div>
            @php
                $columns = [[
                                'label' => 'Shift',
                                'data' => 'shift_input',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'MOD',
                                'data' => 'mod_input',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'Cashier',
                                'data' => 'cashier_input',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'Kitchen',
                                'data' => 'kitchen_input',
                                'class' => 'input'
                            ],
                            [
                                'label' => 'Lobby',
                                'data' => 'lobby_input',
                                'class' => 'input'
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :columns="$columns" url="logbook/operational/duty-roster/dtble?briefing-id={{ $lbBreafings->id }}" footer="false" height="150"/>
        </div>
        <div class="col-12 text-center mt-2">
            <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveBrief()" id="btnSaveBrief{{$dom}}">
                <span>{{ __('Save') }}</span>
            </button>
        </div>
    </div>
</x-form-vertical>

<script>

{{$dom}} = {
   data: {
        id: 0,
        idBrief: {{ $lbBreafings->id }},
    },
    url: {
        save: "logbook/operational/duty-roster",
    },
    event: {
    },
    func: {
        getDataFormBrief: function () {
            var datas = fdtbletabledata{{$dom}}.getAllData();

            var dutyRosterId = [];
            datas.map(function (data) {
                dutyRosterId.push(data.id);
            });

            return {
                'sales_target': $("#sales_target{{$dom}}").val(),
                'highlight': $("#highlight{{$dom}}").val(),
                'mtd_sales': $("#mtd_sales{{$dom}}").val(),
                'rf_updates': $("#rf_updates{{$dom}}").val(),
                'shifts': JSON.stringify($("select[name='lbdtrstshift[]']").map(function(){return $(this).val();}).get()),
                'mods': JSON.stringify($("input[name='lbdtrstmod[]']").map(function(){return $(this).val();}).get()),
                'cashiers': JSON.stringify($("input[name='lbdtrstcashier[]']").map(function(){return $(this).val();}).get()),
                'lobbys': JSON.stringify($("input[name='lbdtrstlobby[]']").map(function(){return $(this).val();}).get()),
                'kitchens': JSON.stringify($("input[name='lbdtrstkitchen[]']").map(function(){return $(this).val();}).get()),
                'duty_roster_id': JSON.stringify(dutyRosterId),
                'id': {{$dom}}.data.idBrief
            }
        },
        saveBrief: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataFormBrief();
            var url = {{$dom}}.url.save;
            url += '/' + {{$dom}}.data.id;
            data._method = 'PUT';

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
