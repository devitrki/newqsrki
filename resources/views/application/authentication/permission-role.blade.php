<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Save" icon="bx bx-save" :onclick="$dom. '.func.save()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Copy Role From Role" icon="bx bx-copy" :onclick="$dom. '.event.copy()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.func.save()" ><i class="bx bx-save mr-50"></i>{{ __('Save') }}</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.copy()" ><i class="bx bx-copy mr-50"></i>{{ __('Copy Role From Role') }}</a>
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
                <x-slot name="right">
                    <x-row-tools>
                        <x-dropdown-export :dom="$dom" dtblecompid="detailmenu" />
                    </x-row-tools>
                    <x-row-tools>
                        <x-input-search :dom="$dom" dtblecompid="detailmenu" />
                    </x-row-tools>
                </x-slot>
            </x-tools>
            @php
                $columns = 
                    [[
                        'label' => 'Name',
                        'data' => 'name',
                    ],[
                        'label' => 'Type',
                        'data' => 'type',
                    ],[
                        'label' => 'URL',
                        'data' => 'url',
                    ],[
                        'label' => 'Description',
                        'data' => 'description',
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="detailmenu" :columns="$columns" url="auth/menu/dtble?parentid=0" :select="[true, 'single']"  number="false" />
        </div>
    </div>
</x-card-scroll>


<!-- modal -->


<!-- end modal -->

<script>


{{$dom}} = {
   
}

</script>