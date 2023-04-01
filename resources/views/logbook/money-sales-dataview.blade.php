<style>
    .custom-switch .custom-control-label:before {
        background-color: red;
    }
</style>
<x-form-vertical :error="false">
    <div class="row mx-0">
        <div class="col-12 text-center mt-1">
            <h5>{{ $shift }} Cashier ( {{ App\Library\Helper::DateConvertFormat($date, 'Y/m/d', 'd-m-Y') }} )</h5>
        </div>
        <div class="col-12">
            <x-divider-text text="CASHIER" />
        </div>
        <div class="col-12 mb-2">
            @php
                $columns = [[
                                'label' => '',
                                'data' => 'cashier_no',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Name',
                                'data' => 'cashier_name',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Opening Cash',
                                'data' => 'opening_cash_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Total Sales',
                                'data' => 'total_sales_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'BCA',
                                'data' => 'bca_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'mandiri',
                                'data' => 'mandiri_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'gopay',
                                'data' => 'go_pay_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'grab pay',
                                'data' => 'grab_pay_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'gobiz',
                                'data' => 'gobiz_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Ovo',
                                'data' => 'ovo_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'shoope pay',
                                'data' => 'shoope_pay_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'shopee food',
                                'data' => 'shopee_food_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'dana',
                                'data' => 'dana_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'voucher',
                                'data' => 'voucher_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'delivery sales',
                                'data' => 'delivery_sales_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'drive thru',
                                'data' => 'drive_thru_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'compliment',
                                'data' => 'compliment_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'total cash in hand',
                                'data' => 'total_cash_hand_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tablecashier" :columns="$columns" url="logbook/money-sales/cashier/dtble?mon-sls-cas-id={{ $lbMonSlsCas->id }}" :select="[true, 'single']" footer="false" height="160" number="false" :dblclick="true"/>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Total Sales">
                <input type="number" class="form-control form-control-sm" id="ctotal_sales{{$dom}}" value="{{ $lbMonSlsCas->total_sales }}">
            </x-row-horizontal>
            <x-row-horizontal label="Total Non Cash">
                <input type="number" class="form-control form-control-sm" id="ctotal_non_cash{{$dom}}" value="{{ $lbMonSlsCas->total_non_cash }}">
            </x-row-horizontal>
            <x-row-horizontal label="Total Cash">
                <input type="number" class="form-control form-control-sm" id="ctotal_cash{{$dom}}" value="{{ $lbMonSlsCas->total_cash }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Brankas Money">
                <input type="number" class="form-control form-control-sm" id="cbrankas_money{{$dom}}" value="{{ $lbMonSlsCas->brankas_money }}">
            </x-row-horizontal>
            <x-row-horizontal label="Pending PC">
                <input type="number" class="form-control form-control-sm" id="cpending_pc{{$dom}}" value="{{ $lbMonSlsCas->pending_pc }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Rp. 100">
                <input type="number" class="form-control form-control-sm" id="c100{{$dom}}" value="{{ $lbMonSlsCas->p100 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 200">
                <input type="number" class="form-control form-control-sm" id="c200{{$dom}}" value="{{ $lbMonSlsCas->p200 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 500">
                <input type="number" class="form-control form-control-sm" id="c500{{$dom}}" value="{{ $lbMonSlsCas->p500 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 1000">
                <input type="number" class="form-control form-control-sm" id="c1000{{$dom}}" value="{{ $lbMonSlsCas->p1000 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 2000">
                <input type="number" class="form-control form-control-sm" id="c2000{{$dom}}" value="{{ $lbMonSlsCas->p2000 }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Rp. 5000">
                <input type="number" class="form-control form-control-sm" id="c5000{{$dom}}" value="{{ $lbMonSlsCas->p5000 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 10000">
                <input type="number" class="form-control form-control-sm" id="c10000{{$dom}}" value="{{ $lbMonSlsCas->p10000 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 20000">
                <input type="number" class="form-control form-control-sm" id="c20000{{$dom}}" value="{{ $lbMonSlsCas->p20000 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 50000">
                <input type="number" class="form-control form-control-sm" id="c50000{{$dom}}" value="{{ $lbMonSlsCas->p50000 }}">
            </x-row-horizontal>
            <x-row-horizontal label="Rp. 100000">
                <input type="number" class="form-control form-control-sm" id="c100000{{$dom}}" value="{{ $lbMonSlsCas->p100000 }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Hand Over By">
                <input type="text" class="form-control form-control-sm" id="chand_over_by{{$dom}}" value="{{ $lbMonSlsCas->hand_over_by }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Received By">
                <input type="text" class="form-control form-control-sm" id="creceived_by{{$dom}}" value="{{ $lbMonSlsCas->received_by }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 text-center my-2">
            <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveCashier()" id="btnSaveCashier{{$dom}}">
                <span>{{ __('Save') }}</span>
            </button>
        </div>
        <div class="col-12">
            <x-divider-text text="Detail Sales" />
        </div>
        <div class="col-12 mb-2">
            <x-tools class="border">
                <x-slot name="left">
                    <x-row-tools>
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.createDetail()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom . '.event.editDetail()'" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.deleteDetail()'" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = [[
                                'label' => 'date',
                                'data' => 'date',
                                'searchable' => 'true',
                                'orderable' => 'false',
                                'format' => 'date',
                            ],
                            [
                                'label' => 'day',
                                'data' => 'day',
                                'searchable' => 'true',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'cash',
                                'data' => 'cash_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'Total non cash',
                                'data' => 'total_non_cash_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'total sales',
                                'data' => 'total_sales_desc',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'hand over by',
                                'data' => 'hand_over_by',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ],
                            [
                                'label' => 'received by',
                                'data' => 'received_by',
                                'searchable' => 'false',
                                'orderable' => 'false',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledetail" :columns="$columns" url="logbook/money-sales/detail/dtble?mon-sls-id={{ $lbMonSls->id }}" :select="[true, 'single']" footer="false" height="160" number="false"/>
        </div>
        <div class="col-12">
            <x-divider-text text="Money Deposit Report" />
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Name">
                <input type="text" class="form-control form-control-sm" id="mname{{$dom}}" value="{{ $lbMonSls->name }}">
            </x-row-horizontal>
            <x-row-horizontal label="NIK">
                <input type="text" class="form-control form-control-sm" id="mnik{{$dom}}" value="{{ $lbMonSls->nik }}">
            </x-row-horizontal>
            <x-row-horizontal label="Function">
                <input type="text" class="form-control form-control-sm" id="mfunction{{$dom}}" value="{{ $lbMonSls->function }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="Total Money">
                <input type="number" class="form-control form-control-sm" id="mtotal_money{{$dom}}" value="{{ $lbMonSls->total_money }}">
            </x-row-horizontal>
            <x-row-horizontal label="Deposit Date">
                <x-pickerdate :dom="$dom" compid="mdeposit_date" data-value="{{ ($lbMonSls->deposit_date != '') ? $lbMonSls->deposit_date : date('Y/m/d') }}" clear="false"/>
            </x-row-horizontal>
            <x-row-horizontal label="Deposit To">
                <select class="form-control form-control-sm" id="mdeposit_to{{$dom}}">
                    @php
                        $options = [
                                    ['value' => 'Bank', 'text' => 'Bank'],
                                    ['value' => 'Other', 'text' => 'Other']
                                ];
                    @endphp
                    @foreach( $options as $i => $opt)
                        @if($opt['value'] == $lbMonSls->deposit_to)
                        <option value="{{$opt['value']}}" selected>{{$opt['text']}}</option>
                        @else
                        <option value="{{$opt['value']}}">{{$opt['text']}}</option>
                        @endif
                    @endforeach
                </select>
            </x-row-horizontal>
        </div>
        <div class="col-12">
            <x-divider-text text="Memo" />
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="DP Ulang Tahun">
                <input type="number" class="form-control form-control-sm" id="mdp_ulang_tahun{{$dom}}" value="{{ $lbMonSls->dp_ulang_tahun }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 col-md-6">
            <x-row-horizontal label="DP Big Order">
                <input type="number" class="form-control form-control-sm" id="mdp_big_order{{$dom}}" value="{{ $lbMonSls->dp_big_order }}">
            </x-row-horizontal>
        </div>
        <div class="col-12 text-center my-2">
            <button type="button" class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveMoneySales()" id="btnSaveMoneySales{{$dom}}">
                <span>{{ __('Save') }}</span>
            </button>
        </div>
    </div>
</x-form-vertical>

<!-- modal -->
<x-modal :dom="$dom" compid="modalcashier" title="Money Sales Handling Cashier" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="ddate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Shift">
                    <input type="text" class="form-control form-control-sm" id="dshift{{$dom}}" disabled>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Cashier">
                    <input type="text" class="form-control form-control-sm" id="dcashier_no{{$dom}}" disabled>
                </x-row-horizontal>
                <x-row-horizontal label="Cashier Name">
                    <input type="text" class="form-control form-control-sm" id="dcashier_name{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Total Sales">
                    <input type="number" class="form-control form-control-sm" id="dtotal_sales{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="BCA">
                    <input type="number" class="form-control form-control-sm" id="dbca{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Mandiri">
                    <input type="number" class="form-control form-control-sm" id="dmandiri{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Gopay">
                    <input type="number" class="form-control form-control-sm" id="dgo_pay{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Grab Pay">
                    <input type="number" class="form-control form-control-sm" id="dgrab_pay{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Gobiz">
                    <input type="number" class="form-control form-control-sm" id="dgobiz{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Ovo">
                    <input type="number" class="form-control form-control-sm" id="dovo{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Shoope Pay">
                    <input type="number" class="form-control form-control-sm" id="dshoope_pay{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Shoope Food">
                    <input type="number" class="form-control form-control-sm" id="dshopee_food{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Dana">
                    <input type="number" class="form-control form-control-sm" id="ddana{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Voucher">
                    <input type="number" class="form-control form-control-sm" id="dvoucher{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Delivery Sales">
                    <input type="number" class="form-control form-control-sm" id="ddelivery_sales{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Drive Thru">
                    <input type="number" class="form-control form-control-sm" id="ddrive_thru{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Compliment">
                    <input type="number" class="form-control form-control-sm" id="dcompliment{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Total Cash In Hand">
                    <input type="number" class="form-control form-control-sm" id="dtotal_cash_hand{{$dom}}">
                </x-row-horizontal>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.saveDetCashier()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>

<x-modal :dom="$dom" compid="modalmanage" title="Money Sales Handling Detail">
    <x-form-horizontal>
        <x-row-horizontal label="Date">
            <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
        </x-row-horizontal>
        <x-row-horizontal label="Day">
            <input type="text" class="form-control form-control-sm" id="day{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Total Cash">
            <input type="number" class="form-control form-control-sm" id="total_cash{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Total Non Cash">
            <input type="number" class="form-control form-control-sm" id="total_non_cash{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Total Sales">
            <input type="number" class="form-control form-control-sm" id="total_sales{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Hand Over By">
            <input type="text" class="form-control form-control-sm" id="hand_over_by{{$dom}}">
        </x-row-horizontal>
        <x-row-horizontal label="Received By">
            <input type="text" class="form-control form-control-sm" id="received_by{{$dom}}">
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
// event double click comp datatable
function callbacktablecashier{{$dom}}(data) {
    {{$dom}}.func.setCashier(data);
}

{{$dom}} = {
   data: {
        idCashier: 0,
        idMonSlsCas: {{ $lbMonSlsCas->id }},
        idMonSls: {{ $lbMonSls->id }},
    },
    url: {
        save: "logbook/money-sales",
    },
    event: {
        createDetail: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        editDetail: function () {
            var rows = fdtbletabledetail{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        deleteDetail: function () {
            var rows = fdtbletabledetail{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
    },
    func: {
        setCashier: function (data) {
            {{$dom}}.data.idCashier = data.id;

            $("#dshift{{$dom}}").val('{{ $shift }}');
            $("#dcashier_no{{$dom}}").val(data.cashier_no);
            $("#dcashier_name{{$dom}}").val(data.cashier_name);
            $("#dopening_cash{{$dom}}").val(data.opening_cash);
            $("#dtotal_sales{{$dom}}").val(data.total_sales);
            $("#dbca{{$dom}}").val(data.bca);
            $("#dmandiri{{$dom}}").val(data.mandiri);
            $("#dgo_pay{{$dom}}").val(data.go_pay);
            $("#dgrab_pay{{$dom}}").val(data.grab_pay);
            $("#dgobiz{{$dom}}").val(data.gobiz);
            $("#dovo{{$dom}}").val(data.ovo);
            $("#dshoope_pay{{$dom}}").val(data.shoope_pay);
            $("#dshopee_food{{$dom}}").val(data.shopee_food);
            $("#ddana{{$dom}}").val(data.dana);
            $("#dvoucher{{$dom}}").val(data.voucher);
            $("#ddelivery_sales{{$dom}}").val(data.delivery_sales);
            $("#ddrive_thru{{$dom}}").val(data.drive_thru);
            $("#dcompliment{{$dom}}").val(data.compliment);
            $("#dtotal_cash_hand{{$dom}}").val(data.total_cash_hand);

            $("#pickerdateddate{{$dom}}").prop("disabled", true);
            pickerdateddate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });

            showModal('modalcashier{{$dom}}');
        },
        saveDetCashier: function () {
            hideErrors();
            loadingModal('start');

            var data = {
                'cashier_name': $("#dcashier_name{{$dom}}").val(),
                'opening_cash': $("#dopening_cash{{$dom}}").val(),
                'total_sales': $("#dtotal_sales{{$dom}}").val(),
                'bca': $("#dbca{{$dom}}").val(),
                'mandiri': $("#dmandiri{{$dom}}").val(),
                'go_pay': $("#dgo_pay{{$dom}}").val(),
                'grab_pay': $("#dgrab_pay{{$dom}}").val(),
                'gobiz': $("#dgobiz{{$dom}}").val(),
                'ovo': $("#dovo{{$dom}}").val(),
                'shoope_pay': $("#dshoope_pay{{$dom}}").val(),
                'shopee_food': $("#dshopee_food{{$dom}}").val(),
                'dana': $("#ddana{{$dom}}").val(),
                'voucher': $("#dvoucher{{$dom}}").val(),
                'delivery_sales': $("#ddelivery_sales{{$dom}}").val(),
                'drive_thru': $("#ddrive_thru{{$dom}}").val(),
                'compliment': $("#dcompliment{{$dom}}").val(),
                'total_cash_hand': $("#dtotal_cash_hand{{$dom}}").val(),
                'id': {{$dom}}.data.idCashier
            };
            var url = {{$dom}}.url.save + '/cashier/det/' + {{$dom}}.data.idCashier;
            data._method = 'PUT';

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletablecashier{{$dom}}.refresh();
                    hideModal('modalcashier{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        saveCashier: function () {
            hideErrors();
            loading('start');

            var data = {
                'total_sales': $("#ctotal_sales{{$dom}}").val(),
                'total_non_cash': $("#ctotal_non_cash{{$dom}}").val(),
                'total_cash': $("#ctotal_cash{{$dom}}").val(),
                'brankas_money': $("#cbrankas_money{{$dom}}").val(),
                'pending_pc': $("#cpending_pc{{$dom}}").val(),
                'hand_over_by': $("#chand_over_by{{$dom}}").val(),
                'received_by': $("#creceived_by{{$dom}}").val(),
                '100': $("#c100{{$dom}}").val(),
                '200': $("#c200{{$dom}}").val(),
                '500': $("#c500{{$dom}}").val(),
                '1000': $("#c1000{{$dom}}").val(),
                '2000': $("#c2000{{$dom}}").val(),
                '5000': $("#c5000{{$dom}}").val(),
                '10000': $("#c10000{{$dom}}").val(),
                '20000': $("#c20000{{$dom}}").val(),
                '50000': $("#c50000{{$dom}}").val(),
                '100000': $("#c100000{{$dom}}").val(),
                'id': {{$dom}}.data.idMonSlsCas
            };

            var url = {{$dom}}.url.save + '/cashier';
            url += '/' + {{$dom}}.data.idMonSlsCas;
            data._method = 'PUT';

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#day{{$dom}}").val('');
            $("#total_cash{{$dom}}").val('');
            $("#total_non_cash{{$dom}}").val('');
            $("#total_sales{{$dom}}").val('');
            $("#hand_over_by{{$dom}}").val('');
            $("#received_by{{$dom}}").val('');
        },
        set: function () {
            var row_data = fdtbletabledetail{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            $("#day{{$dom}}").val(data.day);
            $("#total_cash{{$dom}}").val(data.cash);
            $("#total_non_cash{{$dom}}").val(data.total_non_cash);
            $("#total_sales{{$dom}}").val(data.total_sales);
            $("#hand_over_by{{$dom}}").val(data.hand_over_by);
            $("#received_by{{$dom}}").val(data.received_by);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });

            showModal('modalmanage{{$dom}}');
        },
        getDataForm: function () {
            return {
                'day': $("#day{{$dom}}").val(),
                'total_cash': $("#total_cash{{$dom}}").val(),
                'total_non_cash': $("#total_non_cash{{$dom}}").val(),
                'total_sales': $("#total_sales{{$dom}}").val(),
                'hand_over_by': $("#hand_over_by{{$dom}}").val(),
                'received_by': $("#received_by{{$dom}}").val(),
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'lb_mon_sls_id': {{ $lbMonSls->id }},
                'id': {{$dom}}.data.id
            }
        },
        save: function () {
            hideErrors();
            loadingModal('start');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save + '/detail';
            if( {{$dom}}.data.id != 0 ){
                url += '/' + {{$dom}}.data.id;
                data._method = 'PUT';
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledetail{{$dom}}.refresh();
                    hideModal('modalmanage{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        delete: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletabledetail{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.save + '/detail/' + row[0].id;

            $.post(url, data, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledetail{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');

        },
        saveMoneySales: function () {
            hideErrors();
            loading('start');

            var data = {
                'name': $("#mname{{$dom}}").val(),
                'nik': $("#mnik{{$dom}}").val(),
                'function': $("#mfunction{{$dom}}").val(),
                'total_money': $("#mtotal_money{{$dom}}").val(),
                'deposit_date': pickerdatemdeposit_date{{$dom}}.get('select', 'yyyy/mm/dd'),
                'deposit_to': $("#mdeposit_to{{$dom}}").val(),
                'dp_ulang_tahun': $("#mdp_ulang_tahun{{$dom}}").val(),
                'dp_big_order': $("#mdp_big_order{{$dom}}").val(),
                'id': {{$dom}}.data.idMonSls
            };

            var url = {{$dom}}.url.save;
            url += '/' + {{$dom}}.data.idMonSls;
            data._method = 'PUT';

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    message.success(res.message);
                } else {
                    message.info(res.message);
                }
            }, 'json');
        },
    }
}
</script>
