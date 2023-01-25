<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0 fit-content-tabs">
            <x-tools>
                <x-slot name="left">
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Download" icon="bx bx-download" :onclick="$dom. '.event.download()'" />
                    </x-row-tools>
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Refresh" icon="bx bx-revision" :onclick="'fdtbletabledata'.$dom.'.refresh()'" />
                    </x-row-tools>
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.event.download()" ><i class="bx bx-download mr-50"></i>{{ __('Download') }}</a>
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
                        'label' => 'file type',
                        'data' => 'filetype',
                    ],[
                        'label' => 'report name',
                        'data' => 'name',
                    ],[
                        'label' => 'module',
                        'data' => 'module',
                    ],[
                        'label' => 'status',
                        'data' => 'status',
                    ],[
                        'label' => 'date',
                        'data' => 'created_at',
                        'format' => 'datetime'
                    ]];
            @endphp
            <x-datatable-serverside :dom="$dom" compid="tabledata" :tabmenu="$menu_id" :columns="$columns" url="application/download/dtble" :select="[true, 'single']" />
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
        download: "application/download",
    },
    event: {
        download: function () {
            var rows = fdtbletabledata{{$dom}}.getSelectedCount();
            if( rows < 1  ){
                message.info(" {{ __('validation.table.empty') }} ");
                return false;
            }
            var row_data = fdtbletabledata{{$dom}}.getSelectedData();
            data = row_data[0];
            {{$dom}}.data.id = data.id;

            if( data.status != 'Done'  ){
                message.info(" {{ __('This file not yet download, please wait until done.') }} ");
                return false;
            }

            {{$dom}}.func.download();
        }
    },
    func: {
        download: function () {
            var url = {{$dom}}.url.download + '/' + {{$dom}}.data.id;
            window.open(url);
        }
    }
}

</script>
