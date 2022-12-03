<div {{ $attributes->merge(['class' => 'modal fade']) }} id="{{$compid.$dom}}" role="dialog" aria-labelledby="modalFormManage" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $getSize($size) }}" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-adjust">
                <h6 class="modal-title" id="{{$compid.$dom}}-title">{{ __($title) }}</h6>
                @if($close == 'true')
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
                @endif
            </div>
            <div class="modal-body modal-body-adjust">
                {{ $slot }}
            </div>
            @isset($footer)
            <div class="modal-footer modal-footer-adjust">
                {{ $footer }}
            </div>
            @endisset
        </div>
    </div>
</div>
