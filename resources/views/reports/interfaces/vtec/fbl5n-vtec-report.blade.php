@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('FBL5N Vtec Report') }}</p>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Store Code') }}</th>
                        <th scope="col">{{ __('Store Name') }}</th>
                        @foreach($headers as $header)
                        <th scope="col">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)

                        <tr>
                            <td data-label="{{ __('Store Code') }}">{{ $item['store_code'] }}</td>
                            <td data-label="{{ __('Store Name') }}">{{ $item['store_name'] }}</td>
                            @foreach($headers as $header)
                            <td data-label="{{ $header }}" align="right">{{ App\Library\Helper::convertNumberToInd($item[$header], '', 0) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __($message) }}</h4>
        </div>
    @endif
@endsection
