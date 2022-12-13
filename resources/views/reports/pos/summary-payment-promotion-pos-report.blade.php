@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        @if ($count > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Summary Payment and Promotion Pos Report') }}</p>
            <p class="text-center m-0">{{ $header['store'] }}</p>
            <p class="text-center m-0">{{ $header['date'] }}</p>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">{{ __('Payment | Promotion') }}</th>
                            <th scope="col">{{ __('Qty') }}</th>
                            <th scope="col">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $i => $item)
                            <tr>
                                <td data-label="No">{{ $i+1 }}</td>
                                <td data-label="{{ __('Payment | Promotion') }}">{{ $item->PayTypeName }}</td>
                                <td data-label="Qty" align="right">{{ App\Library\Helper::convertNumberToInd($item->TotalQty, '', 0) }}</td>
                                <td data-label="Amount" align="right">{{ App\Library\Helper::convertNumberToInd($item->PayAmount, '', 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="col-12">
                <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
            </div>
        @endif
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __($message) }}</h4>
        </div>
    @endif
@endsection
