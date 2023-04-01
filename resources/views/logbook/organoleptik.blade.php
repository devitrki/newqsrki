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
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Edit" icon="bx bx-edit" :onclick="$dom. '.event.edit()'" />
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
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.edit()" ><i class="bx bx-edit mr-50"></i>{{ __('Edit') }}</a>
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
                        <x-dropdown-filter :dom="$dom" dtblecompid="tabledata">
                            <div class="col-12">
                                <x-form-vertical>
                                    <x-row-vertical label="Plant">
                                        <x-select :dom="$dom" compid="fplant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" dropdowncompid="tabledata" :default="[$first_plant_id, $first_plant_name]"/>
                                    </x-row-vertical>
                                    <x-row-vertical label="From Date">
                                        <x-pickerdate :dom="$dom" compid="ffromdate" data-value="{{ date('Y/m/d') }}" clear="false" />
                                    </x-row-vertical>
                                    <x-row-vertical label="Until Date">
                                        <x-pickerdate :dom="$dom" compid="funtildate" data-value="{{ date('Y/m/d') }}" clear="false" />
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
                        'data' => 'date_desc',
                        'searchable' => 'false',
                        'orderable' => 'false',
                        'format' => 'date'
                    ],[
                        'label' => 'product',
                        'data' => 'product',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'code',
                        'data' => 'code',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'time',
                        'data' => 'time',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ],[
                        'label' => 'taste',
                        'data' => 'taste',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'aroma',
                        'data' => 'aroma',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'texture',
                        'data' => 'texture',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'color',
                        'data' => 'color',
                        'searchable' => 'false',
                        'orderable' => 'false',
                    ],[
                        'label' => 'pic',
                        'data' => 'pic',
                        'searchable' => 'true',
                        'orderable' => 'false',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="logbook/operational/organoleptik/dtble?plant-id={{$first_plant_id}}&from-date={{ date('Y/m/d') }}&until-date={{ date('Y/m/d') }}" :select="[true, 'single']" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<x-modal :dom="$dom" compid="modalmanage" title="Organoleptik" size="lg">
    <x-form-horizontal>
        <div class="row">
            <div class="col-12">
                <x-divider-text :text="__('Data')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Plant">
                    <x-select :dom="$dom" compid="plant" type="serverside" url="master/plant/select?auth=true&type=outlet" size="sm" :default="[$first_plant_id, $first_plant_name]"/>
                </x-row-horizontal>
                <x-row-horizontal label="Date">
                    <x-pickerdate :dom="$dom" compid="date" data-value="{{ date('Y/m/d') }}" clear="false"/>
                </x-row-horizontal>
                <x-row-horizontal label="Product">
                    <x-select :dom="$dom" compid="product" type="serverside" url="logbook/mapping/product-organoleptik/select?auth=true" size="sm"/>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Code">
                    <input type="text" class="form-control form-control-sm" id="code{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Time">
                    <input type="text" class="form-control form-control-sm" id="time{{$dom}}">
                </x-row-horizontal>
                <x-row-horizontal label="Pic">
                    <input type="text" class="form-control form-control-sm" id="pic{{$dom}}">
                </x-row-horizontal>
            </div>
            <div class="col-12">
                <x-divider-text :text="__('Organoleptik')" class="mt-0" />
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Taste" descId="dtaste{{$dom}}">
                    <select class="form-control form-control-sm" id="taste{{$dom}}">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </x-row-horizontal>
                <x-row-horizontal label="Aroma" descId="daroma{{$dom}}">
                    <select class="form-control form-control-sm" id="aroma{{$dom}}">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </x-row-horizontal>
            </div>
            <div class="col-12 col-md-6">
                <x-row-horizontal label="Texture" descId="dtexture{{$dom}}">
                    <select class="form-control form-control-sm" id="texture{{$dom}}">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </x-row-horizontal>
                <x-row-horizontal label="Color" descId="dcolor{{$dom}}">
                    <select class="form-control form-control-sm" id="color{{$dom}}">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
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
<!-- end modal -->

<script>
$(document).ready(function() {
    $("#time{{$dom}}").inputmask({
        mask: "99:99"
    });
});

$('#select2product{{$dom}}').on('select2:select', function (e) {
    var product = fslctproduct{{$dom}}.get();

    {{$dom}}.func.setDesc(product);
});

{{$dom}} = {
    data: {
        id: 0,
    },
    url: {
        save: "logbook/operational/organoleptik",
        datatable: "logbook/operational/organoleptik/dtble"
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
            {{$dom}}.func.reset();
            {{$dom}}.func.set();
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
        filter: function () {
            var url = {{$dom}}.url.datatable + '?plant-id=' + fslctfplant{{$dom}}.get() + '&from-date=' + pickerdateffromdate{{$dom}}.get('select', 'yyyy/mm/dd') + '&until-date=' + pickerdatefuntildate{{$dom}}.get('select', 'yyyy/mm/dd');
            fdtbletabledata{{$dom}}.changeUrl(url);
            dropdown.hide('hfiltertabledata{{$dom}}');
        },
        setDesc: function (product) {
            loadingModal("start");

            url = 'logbook/mapping/product-organoleptik/detail';
            data = {
                'product' : product
            };

            $.post( url, data, function (res) {
                loadingModal("stop");
                if( res.status == 'success' ){
                    $('#dtaste{{$dom}}').html(res.data.desc_taste);
                    $('#daroma{{$dom}}').html(res.data.desc_aroma);
                    $('#dtexture{{$dom}}').html(res.data.desc_texture);
                    $('#dcolor{{$dom}}').html(res.data.desc_color);
                }
            }, 'json');
        },
        reset: function () {
            {{$dom}}.data.id = 0;

            fslctproduct{{$dom}}.clear();
            $("#code{{$dom}}").val('');
            $("#time{{$dom}}").val('');

            $("#taste{{$dom}}").val('Yes');
            $("#aroma{{$dom}}").val('Yes');
            $("#texture{{$dom}}").val('Yes');
            $("#color{{$dom}}").val('Yes');

            $("#pic{{$dom}}").val('');

            $("#select2plant{{$dom}}").prop("disabled", false);
            $("#pickerdatedate{{$dom}}").prop("disabled", false);
        },
        set: function () {
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            fslctplant{{$dom}}.set(data.plant_id, data.plant);
            pickerdatedate{{$dom}}.set('select', data.date, { format: 'yyyy-mm-dd' });
            fslctproduct{{$dom}}.set(data.product, data.product);

            $("#code{{$dom}}").val(data.code);
            $("#time{{$dom}}").val(data.time);
            $("#taste{{$dom}}").val(data.taste);
            $("#aroma{{$dom}}").val(data.aroma);
            $("#texture{{$dom}}").val(data.texture);
            $("#color{{$dom}}").val(data.color);
            $("#pic{{$dom}}").val(data.pic);

            $("#select2plant{{$dom}}").prop("disabled", true);
            $("#select2material{{$dom}}").prop("disabled", true);
            $("#pickerdatedate{{$dom}}").prop("disabled", true);

            showModal('modalmanage{{$dom}}');

            {{$dom}}.func.setDesc(data.product);
        },
        getDataForm: function () {
            return {
                'date': pickerdatedate{{$dom}}.get('select', 'yyyy/mm/dd'),
                'plant': fslctplant{{$dom}}.get(),

                'product': fslctproduct{{$dom}}.get(),
                'code': $("#code{{$dom}}").val(),
                'time': $("#time{{$dom}}").val(),
                'taste': $("#taste{{$dom}}").val(),
                'aroma': $("#aroma{{$dom}}").val(),
                'texture': $("#texture{{$dom}}").val(),
                'color': $("#color{{$dom}}").val(),
                'pic': $("#pic{{$dom}}").val(),

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
