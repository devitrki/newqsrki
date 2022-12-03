<x-card-scroll>
    <div class="row m-0">
        <div class="col-12 p-0">
            <x-tools>
                <x-slot name="left">
                    @can('u'.$menu_id)
                    <x-row-tools class="d-none d-sm-block">
                        <x-button-tools tooltip="Save" icon="bx bx-save" :onclick="$dom. '.func.save()'" />
                    </x-row-tools>
                    @endcan
                    <x-row-tools class="d-block d-sm-none">
                        <div class="dropdown d-block d-sm-none">
                            <span class="bx bx-menu font-medium-3 dropdown-toggle action-toggle-icon nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('u'.$menu_id)
                                <a class="dropdown-item" href="javascript:void(0)" onclick="{{$dom}}.func.save()" ><i class="bx bx-save mr-50"></i>{{ __('Save') }}</a>
                                @endcan
                            </div>
                        </div>
                    </x-row-tools>
                </x-slot>
            </x-tools>
            <x-form-horizontal>
                <div class="row mx-0">
                    @foreach($configurations as $app)
                    <div class="col-12">
                        <x-divider-text :text="__($app->name)" />
                    </div>
                        @foreach($app->configurations as $a)
                            <div class="col-12 col-md-6">
                                <x-row-horizontal :label="$a->label" :desc="$a->description">
                                @if( $a->type == 'select' )
                                    <select class="form-control form-control-sm" id="{{ $a->key . $dom }}">
                                        @foreach( json_decode($a->option) as $opt)
                                            @if( $a->value != $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                            @else
                                            <option value="{{ $opt }}" selected="selected">{{ $opt }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @elseif( $a->type == 'textarea' )
                                    <textarea class="form-control form-control-sm" id="{{ $a->key . $dom }}" rows="3">{{ $a->value }}</textarea>
                                @elseif( $a->type == 'select2' )
                                    <x-select :dom="$dom" compid="{{ $a->key }}" type="serverside" async="false" url="{{ $a->option }}" size="sm" :default="[$a->value, '']"  />
                                @else
                                    <input type="text" class="form-control form-control-sm" id="{{ $a->key . $dom }}" value="{{ $a->value }}">
                                @endif
                                </x-row-horizontal>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </x-form-horizontal>
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
        save: "master/master-configuration",
    },
    event: {
    },
    func: {
        getDataForm: function () {
            return {
                @foreach($configurations as $app)
                    @foreach($app->configurations as $a)
                        @if( $a->type == 'select2' )
                            {{ $a->key }} : fslct{{ $a->key . $dom }}.get(),
                        @else
                            {{ $a->key }} : $('#{{ $a->key . $dom }}').val(),
                        @endif
                    @endforeach
                @endforeach
            };
        },
        save: function () {
            loading('start', '{{ __("Save") }}', 'process');

            var data = {{$dom}}.func.getDataForm();
            var url = {{$dom}}.url.save;

            $.post( url, data, function (res) {
                loading("stop");
                if( res.status == 'success' ){
                    message.success(res.message);
                } else {
                    message.failed(res.message);
                }
            }, 'json');
        }
    }
}

</script>
