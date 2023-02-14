<x-card-scroll>
    <div class="row m-0 m-2">
        <div class="col-12 p-0">
            <h3 class="mb-2">Trigger Callback Authorization Code Accurate</h3>

            <form class="form form-horizontal" action="https://accurate.id/oauth/authorize" method="post" target="_blank">
                <div class="form-body">
                    <x-row-horizontal label="Client ID">
                    <input class="form-control form-control-sm" name="client_id" value="{{ $client_id }}" />
                    </x-row-horizontal>
                    <x-row-horizontal label="Response Type">
                        <input class="form-control form-control-sm" name="response_type" value="code" />
                    </x-row-horizontal>
                    <x-row-horizontal label="Redirect URI">
                        <input class="form-control form-control-sm" name="redirect_uri" value="{{ $redirect_uri }}" />
                    </x-row-horizontal>
                    <x-row-horizontal label="Scope">
                        <input class="form-control form-control-sm" name="scope" value="item_view item_save sales_invoice_view sales_invoice_save sales_receipt_save sales_return_save" />
                    </x-row-horizontal>

                    <button class="btn btn-secondary btn-sm" type="submit">
                        <span>{{ __('Submit') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-card-scroll>

<!-- modal -->
<!-- end modal -->

<script>
{{$dom}} = {
    data: {
    },
    url: {
    },
    event: {
    },
    func: {
    }
}

</script>
