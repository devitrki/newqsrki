<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @hasanyrole('purchasing staff|superadmin')
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    @endcan
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Submit" icon="bx bx-upload" :onclick="$dom. '.event.submit()'" />
                    </x-row-tools>
                    @endcan
                    @can('d'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.delete()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject Description" icon="bx bx-info-circle" :onclick="$dom. '.event.rejectDesc()'" />
                    </x-row-tools>
                    @endhasanyrole
                    @hasanyrole('finance staff|superadmin')
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Approve" icon="bx bx-check" :onclick="$dom. '.event.approve()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Reject" icon="bx bx-x" :onclick="$dom. '.event.reject()'" />
                    </x-row-tools>
                    @endcan
                    @endhasanyrole
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @hasanyrole('purchasing staff|superadmin')
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Create') }}</a>
                                @endcan
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.submit()" ><i class="bx bx-upload mr-50"></i>{{ __('Submit') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.rejectDesc()" ><i class="bx bx-info-circle mr-50"></i>{{ __('Reject Description') }}</a>
                                @endcan
                                @endhasanyrole
                                @hasanyrole('finance staff|superadmin')
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.approve()" ><i class="bx bx-check mr-50"></i>{{ __('Approve') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.reject()" ><i class="bx bx-x mr-50"></i>{{ __('Reject') }}</a>
                                @endcan
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
                                    <x-row-vertical label="Vendor">
                                        <x-select :dom="$dom" compid="fvendor" type="serverside" url="inventory/usedoil/uo-vendor/select?ext=all" size="sm" dropdowncompid="tabledata" :default="[0, __('All') ]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="From">
                                        <x-pickerdate :dom="$dom" compid="ffrom" data-value="{{ date('Y/m/d', strtotime('-30 days')) }}" clear="false"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="Until">
                                        <x-pickerdate :dom="$dom" compid="funtil" data-value="{{ date('Y/m/d') }}" clear="false" />
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
                        'label' => 'Submit',
                        'data' => 'submit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Confirmation FA',
                        'data' => 'confirmation_fa_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Document Number',
                        'data' => 'document_number',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Vendor',
                        'data' => 'vendor_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Deposit Date',
                        'data' => 'deposit_date',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date',
                    ],[
                        'label' => 'Richeese Bank',
                        'data' => 'richeese_bank',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'nominal',
                        'data' => 'deposit_nominal_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Deposit Type',
                        'data' => 'type_deposit_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Bank',
                        'data' => 'transfer_bank',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Bank Account',
                        'data' => 'transfer_bank_account',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Bank Account Name',
                        'data' => 'transfer_bank_account_name',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'Image Transfer',
                        'data' => 'image_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-deposit/dtble?vendor-id=0&from={{ date('Y/m/d', strtotime('-30 days')) }}&until={{ date('Y/m/d') }}" :select="[true, 'single']"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Vendor Deposit" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Vendor">
                    <x-select :dom="$dom" compid="vendor" type="serverside" url="inventory/usedoil/uo-vendor/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Type">
                    @php
                        $options = [
                                    ['id' => 1, 'text' => \Lang::get("Deposit Cash")],
                                    ['id' => 2, 'text' => \Lang::get("Bank Transfer")]
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="deposit_type" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Richeese Bank">
                    @php
                        $options = [];

                        foreach ($bank_richeese as $v) {
                            $options[] = ['id' => $v, 'text' => $v];
                        }
                    @endphp
                    <x-select :dom="$dom" compid="richeese_bank" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Nominal">
                    <input type="number" class="form-control form-control-sm" id="nominal{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Image Transfer">
                    <input type="file" class="form-control-file" id="image_transfer{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 desc_transfer d-none">
                <x-divider-text :text="__('Transfer Vendor Description')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank">
                    <input type="text" class="form-control form-control-sm" id="bank{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Bank Account Number">
                    <input type="text" class="form-control form-control-sm" id="bank_account_number{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank Account Under the Name (of)">
                    <input type="text" class="form-control form-control-sm" id="bank_account_name{{$dom}}">
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

<x-modal :dom="$dom" compid="modaldesc" title="Description Reject" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Vendor">
                    <x-select :dom="$dom" compid="rvendor" type="serverside" url="inventory/usedoil/uo-vendor/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Date">
                    <x-pickerdate :dom="$dom" compid="rdate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Type">
                    @php
                        $options = [
                                    ['id' => 1, 'text' => \Lang::get("Deposit Cash")],
                                    ['id' => 2, 'text' => \Lang::get("Bank Transfer")]
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="rdeposit_type" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Richeese Bank">
                    @php
                        $options = [];

                        foreach ($bank_richeese as $v) {
                            $options[] = ['id' => $v, 'text' => $v];
                        }
                    @endphp
                    <x-select :dom="$dom" compid="rricheese_bank" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Nominal">
                    <input type="number" class="form-control form-control-sm" id="rnominal{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 desc_transfer d-none">
                <x-divider-text :text="__('Transfer Vendor Description')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank">
                    <input type="text" class="form-control form-control-sm" id="rbank{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Bank Account Number">
                    <input type="text" class="form-control form-control-sm" id="rbank_account_number{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank Account Under the Name (of)">
                    <input type="text" class="form-control form-control-sm" id="rbank_account_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Reject Description')" class="mt-0" />
            </div>
            <div class="col-12">
                <div class="col-12 mb-2">
                    <textarea class="form-control form-control-sm" id="desc_reject{{$dom}}" rows="4" readonly></textarea>
                </div>
            </div>
        </div>
    </x-form-horizontal>
</x-modal>

<x-modal :dom="$dom" compid="modalreject" title="Reject" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Vendor">
                    <x-select :dom="$dom" compid="rjvendor" type="serverside" url="inventory/usedoil/uo-vendor/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Date">
                    <x-pickerdate :dom="$dom" compid="rjdate" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Deposit Type">
                    @php
                        $options = [
                                    ['id' => 1, 'text' => \Lang::get("Deposit Cash")],
                                    ['id' => 2, 'text' => \Lang::get("Bank Transfer")]
                                ];
                    @endphp
                    <x-select :dom="$dom" compid="rjdeposit_type" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Richeese Bank">
                    @php
                        $options = [];

                        foreach ($bank_richeese as $v) {
                            $options[] = ['id' => $v, 'text' => $v];
                        }
                    @endphp
                    <x-select :dom="$dom" compid="rjricheese_bank" type="array" :options="$options" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Nominal">
                    <input type="number" class="form-control form-control-sm" id="rjnominal{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 desc_transfer d-none">
                <x-divider-text :text="__('Transfer Vendor Description')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank">
                    <input type="text" class="form-control form-control-sm" id="rjbank{{$dom}}" readonly>
                </x-row-horizontal>
                <x-row-horizontal label="Bank Account Number">
                    <input type="text" class="form-control form-control-sm" id="rjbank_account_number{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6 desc_transfer d-none">
                <x-row-horizontal label="Bank Account Under the Name (of)">
                    <input type="text" class="form-control form-control-sm" id="rjbank_account_name{{$dom}}" readonly>
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Reject Description')" class="mt-0" />
            </div>
            <div class="col-12 mb-2">
                <textarea class="form-control form-control-sm" id="reject_description{{$dom}}" rows="4"></textarea>
            </div>
        </div>
    </x-form-horizontal>

    <x-slot name="footer">
        <button class="btn btn-light btn-sm" data-dismiss="modal">
            <span>{{ __('Cancel') }}</span>
        </button>
        <button class="btn btn-secondary ml-1 btn-sm" onclick="{{$dom}}.func.reject()">
            <span>{{ __('Save') }}</span>
        </button>
    </x-slot>
</x-modal>
<!-- end modal -->

<script>

$('#select2deposit_type{{$dom}}').on('select2:select', function (e) {
    var data = e.params.data;
    if( data.id != '1' ){
        $('.desc_transfer').removeClass( "d-none" );
    } else {
        $('.desc_transfer').addClass( "d-none" );
    }
});

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/usedoil/uo-deposit",
        datatable: "inventory/usedoil/uo-deposit/dtble"
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();
            showModal('modalmanage{{$dom}}');
        },
        edit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('this transaction already submitted, cannot edited.') }} ");
                return false;
            }

            {{$dom}}.func.reset();
            {{$dom}}.func.set();
        },
        rejectDesc: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.confirmation_fa != '2' ){
                message.info(" {{ __('this transaction was not rejected.') }} ");
                return false;
            }

            {{$dom}}.func.showReject();
        },
        reject: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit == '0' ){
                message.info(" {{ __('This transaction has not been submitted.') }} ");
                return false;
            }
            if( data.confirmation_fa == '1' ){
                message.info(" {{ __('This transaction has been approved.') }} ");
                return false;
            }
            if( data.confirmation_fa == '2' ){
                message.info(" {{ __('This transaction has been rejected.') }} ");
                return false;
            }

            {{$dom}}.func.setReject();
        },
        submit: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('This transaction has been submitted.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been submitted cannot be restored.",
                            "{{$dom}}.func.submit");
        },
        approve: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit == '0' ){
                message.info(" {{ __('This transaction has not been submitted.') }} ");
                return false;
            }
            if( data.confirmation_fa == '1' ){
                message.info(" {{ __('This transaction has been approved.') }} ");
                return false;
            }
            if( data.confirmation_fa == '2' ){
                message.info(" {{ __('This transaction has been rejected.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been approved cannot be restored.",
                            "{{$dom}}.func.approve");
        },
        delete: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }

            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];

            if( data.submit != '0' ){
                message.info(" {{ __('This transaction has been submitted, cannot deleted.') }} ");
                return false;
            }

            message.confirm("Are you sure ?",
                            "Data that has been deleted cannot be restored.",
                            "{{$dom}}.func.delete");

        },
    },
    func: {
        filter: function () {
            var url = {{$dom}}.url.datatable + '?vendor-id=' + fslctfvendor{{$dom}}.get() + '&from=' + pickerdateffrom{{$dom}}.get('select', 'yyyy/mm/dd') + '&until=' + pickerdatefuntil{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        reset: function () {
            {{$dom}}.data.id = 0;
            fslctvendor{{$dom}}.clear();
            pickerdatedate{{$dom}}.set('select', '{{ date("Y-m-d") }}', { format: 'yyyy-mm-dd' });
            fslctdeposit_type{{$dom}}.set('1', 'Deposit Cash');
            fslctricheese_bank{{$dom}}.set('BCA', 'BCA');
            $("#bank{{$dom}}").val('');
            $("#bank_account_number{{$dom}}").val('');
            $("#bank_account_name{{$dom}}").val('');
            $("#nominal{{$dom}}").val('');
            $("#image_transfer{{$dom}}").val('');

            $('.desc_transfer').addClass( "d-none" );
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctvendor{{$dom}}.set(data.uo_vendor_id, data.vendor_name);
            pickerdatedate{{$dom}}.set('select', data.deposit_date, { format: 'yyyy-mm-dd' });
            if(data.type_deposit != '1'){
                fslctdeposit_type{{$dom}}.set(data.type_deposit, 'Bank Transfer');
                $('.desc_transfer').removeClass( "d-none" );
            } else {
                fslctdeposit_type{{$dom}}.set(data.type_deposit, 'Deposit Cash');
                $('.desc_transfer').addClass( "d-none" );
            }
            fslctricheese_bank{{$dom}}.set(data.richeese_bank, data.richeese_bank);
            $("#bank{{$dom}}").val(data.transfer_bank);
            $("#bank_account_number{{$dom}}").val(data.transfer_bank_account);
            $("#bank_account_name{{$dom}}").val(data.transfer_bank_account_name);
            $("#nominal{{$dom}}").val(data.deposit_nominal);
            showModal('modalmanage{{$dom}}');
        },
        showReject: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctrvendor{{$dom}}.set(data.uo_vendor_id, data.vendor_name);
            pickerdaterdate{{$dom}}.set('select', data.deposit_date, { format: 'yyyy-mm-dd' });
            if(data.type_deposit != '1'){
                fslctrdeposit_type{{$dom}}.set(data.type_deposit, 'Bank Transfer');
                $('.desc_transfer').removeClass( "d-none" );
            } else {
                fslctrdeposit_type{{$dom}}.set(data.type_deposit, 'Deposit Cash');
                $('.desc_transfer').addClass( "d-none" );
            }
            fslctrricheese_bank{{$dom}}.set(data.richeese_bank, data.richeese_bank);
            $("#rbank{{$dom}}").val(data.transfer_bank);
            $("#rbank_account_number{{$dom}}").val(data.transfer_bank_account);
            $("#rbank_account_name{{$dom}}").val(data.transfer_bank_account_name);
            $("#rnominal{{$dom}}").val(data.deposit_nominal);

            $("#select2rvendor{{$dom}}").prop("disabled", true);
            $("#select2rricheese_bank{{$dom}}").prop("disabled", true);
            $("#select2rdeposit_type{{$dom}}").prop("disabled", true);
            $("#pickerdaterdate{{$dom}}").prop("disabled", true);

            $("#desc_reject{{$dom}}").val(data.reject_description);
            showModal('modaldesc{{$dom}}');
        },
        setReject: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctrjvendor{{$dom}}.set(data.uo_vendor_id, data.vendor_name);
            pickerdaterjdate{{$dom}}.set('select', data.deposit_date, { format: 'yyyy-mm-dd' });
            if(data.type_deposit != '1'){
                fslctrjdeposit_type{{$dom}}.set(data.type_deposit, 'Bank Transfer');
                $('.desc_transfer').removeClass( "d-none" );
            } else {
                fslctrjdeposit_type{{$dom}}.set(data.type_deposit, 'Deposit Cash');
                $('.desc_transfer').addClass( "d-none" );
            }
            fslctrjricheese_bank{{$dom}}.set(data.richeese_bank, data.richeese_bank);
            $("#rjbank{{$dom}}").val(data.transfer_bank);
            $("#rjbank_account_number{{$dom}}").val(data.transfer_bank_account);
            $("#rjbank_account_name{{$dom}}").val(data.transfer_bank_account_name);
            $("#rjnominal{{$dom}}").val(data.deposit_nominal);

            $("#select2rjvendor{{$dom}}").prop("disabled", true);
            $("#select2rjricheese_bank{{$dom}}").prop("disabled", true);
            $("#select2rjdeposit_type{{$dom}}").prop("disabled", true);
            $("#pickerdaterjdate{{$dom}}").prop("disabled", true);

            $("#reject_description{{$dom}}").val('');
            showModal('modalreject{{$dom}}');
        },
        getDataForm: function () {
            var fd = new FormData();
            var image = $('#image_transfer{{$dom}}')[0].files[0];
            fd.append('image_transfer',image);
            fd.append('vendor', fslctvendor{{$dom}}.get());
            fd.append('deposit_date', pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'));
            fd.append('deposit_type', fslctdeposit_type{{$dom}}.get());
            fd.append('richeese_bank', fslctricheese_bank{{$dom}}.get());
            fd.append('nominal', $("#nominal{{$dom}}").val());
            fd.append('bank', $("#bank{{$dom}}").val());
            fd.append('bank_account_number', $("#bank_account_number{{$dom}}").val());
            fd.append('bank_account_under_the_name', $("#bank_account_name{{$dom}}").val());
            fd.append('id', {{$dom}}.data.id);
            return fd;
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

            $.ajax({
                url: url,
                type: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(res){
                    loadingModal("stop");
                    if( res.status == 'success' ){
                        fdtbletabledata{{$dom}}.refresh();
                        hideModal('modalmanage{{$dom}}');
                        message.success(res.message);
                    } else {
                        message.failed(res.message);
                    }
                },
            });
        },
        reject: function () {
            hideErrors();
            loadingModal('start');

            var url = {{$dom}}.url.save + '/reject/' + {{$dom}}.data.id;

            data = {
                'reject_description': $("#reject_description{{$dom}}").val(),
                'id': {{$dom}}.data.id,
            }

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    fdtbletabledata{{$dom}}.refresh();
                    hideModal('modalreject{{$dom}}');
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        },
        submit: function () {
            loading('start', '{{ __("Submit") }}', 'process');

            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/submit/' + row[0].id;

            $.get(url, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');
        },
        approve: function () {
            loading('start', '{{ __("Approve") }}', 'process');

            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.save + '/approve/' + row[0].id;

            $.get(url, function (res) {
                loading("stop");
                if (res.status == 'success') {
                    fdtbletabledata{{$dom}}.refresh();
                    message.success(res.message);
                } else {
                    message.warning(res.message);
                }
            }, 'json');
        },
        delete: function () {
            loading('start', '{{ __("Delete") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var data = {
                _method: 'DELETE',
            };

            var url = {{$dom}}.url.save + '/' + row[0].id;

            $.post(url, data, function (res) {
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
