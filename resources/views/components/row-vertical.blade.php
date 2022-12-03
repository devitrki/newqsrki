<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>{{ __($label) }}</label>
            {{ $slot }}
            @if($desc != "")
                <p class="m-0"><small class="text-muted">{{__($desc)}}</small></p>
            @endif
        </div>
    </div>
</div>