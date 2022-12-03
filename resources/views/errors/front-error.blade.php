<div class="card m-0 card-tabs bg-full-screen-image">
    <div class="card-content fit-screen-tabs">
        <row class="row flexbox-container fit-screen-tabs">
            <div class="col-xl-6 col-md-8 col-10">
                <!-- w-100 for IE specific -->
                <div class="card bg-transparent shadow-none">
                    <div class="card-content">
                        <div class="card-body text-center">
                            <img src="{{asset('images/logo/not-found.png')}}" class="img-fluid mt-1 mb-1" alt="branding logo">
                            <h1 class="error-title mt-1">Oops...</h1>
                            <h1 class="error-title mt-1">{{ __('Something went wrong') }}</h1>
                            <p class="p-0">{{ __('Try to refresh this page or contact IT') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </row>
    </div>
</div>