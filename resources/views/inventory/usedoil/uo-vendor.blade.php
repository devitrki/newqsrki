<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('c'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Create" icon="bx bx-plus-circle" :onclick="$dom. '.event.create()'" />
                    </x-row-tools>
                    @endcan
                    @can('d'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Delete" icon="bx bx-trash" :onclick="$dom. '.event.delete()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('c'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.create()" ><i class="bx bx-plus-circle mr-50"></i>{{ __('Create') }}</a>
                                @endcan
                                @can('d'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.delete()" ><i class="bx bx-trash mr-50"></i>{{ __('Delete') }}</a>
                                @endcan
                                <a class="dropdown-item" href="javascript:void(0)" onclick=fdtbletabledata{{$dom}}.refresh()><i class="bx bx-revision mr-50"></i>{{ __('Refresh') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
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
                        'label' => 'name',
                        'data' => 'name',
                    ],[
                        'label' => 'categori price',
                        'data' => 'uo_category_price_name',
                    ],[
                        'label' => 'contact person',
                        'data' => 'contact_person',
                    ],[
                        'label' => 'phone',
                        'data' => 'phone',
                    ],[
                        'label' => 'address',
                        'data' => 'address',
                    ],[
                        'label' => 'city',
                        'data' => 'city',
                    ],[
                        'label' => 'province',
                        'data' => 'province',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="inventory/usedoil/uo-vendor/dtble" :select="[true, 'single']" :dblclick="true"/>
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Vendor Used Oil" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="Name">
                    <input type="text" class="form-control form-control-sm" id="name{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Category Price">
                    <x-select :dom="$dom" compid="category_price" type="serverside" url="inventory/usedoil/uo-price-category/select" size="sm"/>
                </x-row-horizontal>
                <x-row-horizontal label="Contact Person">
                    <input type="text" class="form-control form-control-sm" id="contact_person{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="phone">
                    <input type="text" class="form-control form-control-sm" id="phone{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12 col-sm-6">
                <x-row-horizontal label="address">
                    <textarea class="form-control form-control-sm" id="address{{$dom}}" rows="3"></textarea>
                </x-row-horizontal>
                <x-row-horizontal label="city">
                    <input type="text" class="form-control form-control-sm" id="city{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="province">
                    <input type="text" class="form-control form-control-sm" id="province{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Mapping Plants')" class="mt-0" />
            </div>
            <div class="col-12">
                <x-tools class="border">
                    <x-slot name="left">
                        <x-row-tools>
                            <div style="width: 20rem">
                                <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?select=code" size="sm"/>
                            </div>
                        </x-row-tools>
                        <x-row-tools>
                            <x-button-tools tooltip="Add" icon="bx bx-plus-circle" :onclick="$dom. '.func.addPlant()'" />
                        </x-row-tools>
                        <x-row-tools>
                            <x-button-tools tooltip="Delete Plant" icon="bx bx-trash" :onclick="$dom. '.func.removePlant()'" />
                        </x-row-tools>
                    </x-slot>
                </x-tools>
                @php
                    $columns =
                        [[
                            'label' => 'Plant Code',
                            'data' => 'plant_code',
                        ],[
                            'label' => 'Plant name',
                            'data' => 'plant_name',
                        ]];
                @endphp
                <x-datatable-source :dom="$dom" compid="tableplant" :columns="$columns" compidmodal="modalmanage" footer="false" height="300" :select="[true, 'single']" />
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
<!-- end modal -->

<script>

// event double click comp datatable
function callbacktabledata{{$dom}}(data) {
    {{$dom}}.func.set(data);
}

{{$dom}} = {
   data: {
        id: 0,
    },
    url: {
        save: "inventory/usedoil/uo-vendor",
    },
    event: {
        create: function () {
            {{$dom}}.func.reset();
            {{$dom}}.func.setTablePlant('0');
            showModal('modalmanage{{$dom}}');
        },
        delete: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
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
        reset: function () {
            {{$dom}}.data.id = 0;
            $("#name{{$dom}}").val('');
            fslctcategory_price{{$dom}}.clear();
            fslctplant{{$dom}}.clear();
            $("#contact_person{{$dom}}").val('');
            $("#phone{{$dom}}").val('');
            $("#address{{$dom}}").val('');
            $("#city{{$dom}}").val('');
            $("#province{{$dom}}").val('');
        },
        set: function (data) {

            {{$dom}}.data.id = data.id;

            $("#name{{$dom}}").val(data.name);
            fslctcategory_price{{$dom}}.set(data.uo_category_price_id, data.uo_category_price_name);
            $("#contact_person{{$dom}}").val(data.contact_person);
            $("#phone{{$dom}}").val(data.phone);
            $("#address{{$dom}}").val(data.address);
            $("#city{{$dom}}").val(data.city);
            $("#province{{$dom}}").val(data.province);

            {{$dom}}.func.setTablePlant(data.id);

            showModal('modalmanage{{$dom}}');
        },
        setTablePlant: function (id) {
            fdtbletableplant{{$dom}}.clear();
            if( id != '0'){
                loadingModal('start');
                $.get( 'inventory/usedoil/uo-vendor/plant/' + id, function (res) {
                    for (let i = 0; i < res.data.length; i++) {
                        fdtbletableplant{{$dom}}.add([
                            '',
                            res.data[i].code,
                            res.data[i].initital + ' ' + res.data[i].short_name
                        ]);
                    }
                    fdtbletableplant{{$dom}}.refresh();
                    loadingModal('stop');
                });
            } else {
                fdtbletableplant{{$dom}}.refresh();
            }
        },
        addPlant: function () {
            var plant = $("#select2plant{{$dom}}").select2('data');
            if( plant.length <= 0){
                message.info('{{ __("Please select plant first") }}');
                return false;
            }

            // check key exist
            var dataExist = fdtbletableplant{{$dom}}.getArrayData();
            funique = true;
            for (let i = 0; i < dataExist.length; i++) {
                if(dataExist[i][1] == plant[0].id){
                    funique = false;
                    break;
                }
            }
            if (!funique) {
                message.info(" {{ __('Plant is already') }} ");
                return false;
            }

            fdtbletableplant{{$dom}}.add([
                '',
                plant[0].id,
                plant[0].text
            ]);
            fdtbletableplant{{$dom}}.refresh();

            fslctplant{{$dom}}.clear();
        },
        removePlant: function () {
            var rows = fdtbletableplant{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            var index = fdtbletableplant{{$dom}}.getRowIndex();
            fdtbletableplant{{$dom}}.remove(index);
            fdtbletableplant{{$dom}}.refresh();
        },
        getDataForm: function () {
            var dplants = fdtbletableplant{{$dom}}.getAllData();
            var plants = [];
            for (let i = 0; i < dplants.length; i++) {
                plants.push(dplants[i][1]);
            }
            return {
                'name': $("#name{{$dom}}").val(),
                'category_price': fslctcategory_price{{$dom}}.get(),
                'contact_person': $("#contact_person{{$dom}}").val(),
                'phone': $("#phone{{$dom}}").val(),
                'address': $("#address{{$dom}}").val(),
                'city': $("#city{{$dom}}").val(),
                'province': $("#province{{$dom}}").val(),
                'plants': (plants.length < 1) ? "" : JSON.stringify(plants),
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
                    message.failed(res.message);
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
