@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        @if ($count > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Void (Refund) Pos Report') }}</p>
            <p class="text-center m-0">{{ $header['store'] }}</p>
            <p class="text-center m-0">{{ $header['date_from'] . ' - ' . $header['date_until'] }}</p>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Reciept Number') }}</th>
                            <th scope="col">{{ __('Total Payment') }}</th>
                            <th scope="col">{{ __('Void Time') }}</th>
                            <th scope="col">{{ __('Void Staff') }}</th>
                            <th scope="col">{{ __('Void Reason') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td data-label="{{ __('Date') }}">{{ App\Library\Helper::DateConvertFormat($item->SaleDate, 'Y-m-d', 'd/m/Y') }}</td>
                                <td data-label="{{ __('Reciept Number') }}">{{ $item->ReceiptNumber }}</td>
                                <td data-label="{{ __('Total Payment') }}" align="right"> {{ App\Library\Helper::convertNumberToInd( $item->PayAmount , '', 0) }}</td>
                                <td data-label="{{ __('Void Time') }}">{{ $item->time }}</td>
                                <td data-label="{{ __('Void Staff') }}">{{ $item->VoidStaff }}</td>
                                <td data-label="{{ __('Void Reason') }}">{{ $item->VoidReason }}</td>
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
