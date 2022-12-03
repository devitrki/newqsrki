<div class="row">
    <div class="col-md-4">
        @if($id != '')
        <label id="{{$id}}">{{ __($label) }}</label>
        @else
        <label>{{ __($label) }}</label>
        @endif

    </div>
    <div class="col-md-8 form-group">
        {{ $slot }}
        @if($desc != "" || $descId != "")
        <p class="m-0">
            @if($descId != "")
                <small class="text-muted" id="{{$descId}}">{{__($desc)}}</small>
            @else
                <small class="text-muted">{{__($desc)}}</small>
            @endif
        </p>
        @endif
    </div>
</div>
