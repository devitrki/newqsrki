<form class="form form-horizontal">
    <div class="form-body">
        @if($error)
        <div class="alert alert-danger d-none errors"> </div>
        @endif
        {{ $slot }}
    </div>
</form>
