<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Sync" icon="bx bx-sync" :onclick="$dom. '.func.sync()'" />
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
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.func.sync()" ><i class="bx bx-sync mr-50"></i>{{ __('Sync') }}</a>
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
                $columns = [[
                                'label' => 'Code',
                                'data' => 'code',
                            ],
                            [
                                'label' => 'Description',
                                'data' => 'description',
                            ],
                            [
                                'label' => 'Type',
                                'data' => 'type',
                            ],
                            [
                                'label' => 'Group',
                                'data' => 'group',
                            ],
                            [
                                'label' => 'UOM',
                                'data' => 'uom',
                            ],
                            [
                                'label' => 'Alternative OUM',
                                'data' => 'alternative_uom',
                            ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="master/material/dtble" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->
<!-- end modal -->

<script>
{{$dom}} = {
    data: {
        id: 0,
    },
    url: {
        sync: "master/material/sync",
    },
    event: {
    },
    func: {
        sync: function () {
            loading('start', '{{ __("Sync") }}', 'process');
            var row = fdtbletabledata{{$dom}}.getSelectedData();

            var url = {{$dom}}.url.sync;

            $.get(url, function (res) {
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
