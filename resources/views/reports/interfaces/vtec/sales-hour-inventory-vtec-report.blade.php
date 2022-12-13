@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        @if ($count > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Sales By Inventory Per Hour Vtec Report') }}</p>
            <p class="text-center m-0">{{ $header['store'] }}</p>
            <p class="text-center m-0">{{ $header['date_from'] . ' - ' . $header['date_until'] }}</p>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Product Code') }}</th>
                            <th scope="col">{{ __('Product Name') }}</th>
                            @for($time = 0; $time <= 23; $time++)
                            <th scope="col">{{ sprintf("%02d", $time) . '-' . sprintf("%02d", ($time + 1 > 23) ? 0 : $time + 1) }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $productCode => $item)
                            <tr>
                                <td data-label="{{ __('Product Code') }}">{{ $productCode }}</td>
                                <td data-label="{{ __('Product Name') }}">{{ $item['ProductName'] }}</td>
                                @for($time = 0; $time <= 23; $time++)
                                @isset( $item['h'.$time] )
                                <td data-label="{{ sprintf("%02d", $time) . '-' . sprintf("%02d", ($time + 1 > 23) ? 0 : $time + 1) }}" align="right">
                                    {{ App\Library\Helper::convertNumberToInd( $item['h'.$time] , '', 0) }}
                                </td>
                                @else
                                <td data-label="{{ sprintf("%02d", $time) . '-' . sprintf("%02d", ($time + 1 > 23) ? 0 : $time + 1) }}" align="right">
                                    0
                                </td>
                                @endisset
                                @endfor
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
