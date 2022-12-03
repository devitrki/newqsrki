<form class="form form-vertical">
    <div class="form-body">
        @if($error == 'true')
        <div class="alert alert-danger d-none errors"> </div>
        @endif
        {{ $slot }}
    </div>
</form>
