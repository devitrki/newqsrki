@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Promotion Type POS Report') }}</p>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ __('PROMOTION NAME') }}</th>
                        <th>{{ __('STORE NAME') }}</th>
                        <th>{{ __('BILL') }}</th>
                        <th>{{ __('PRODUCT CODE') }}</th>
                        <th>{{ __('PRODUCT NAME') }}</th>
                        <th>{{ __('QTY') }}</th>
                        <th>{{ __('TOTAL RETAIL PRICE') }}</th>
                        <th>{{ __('DISCOUNT') }}</th>
                        <th>{{ __('NET SALES') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $listItems)
                        @foreach ($listItems as $item)
                            <tr>
                                <td>{{ $item->PromotionName }}</td>
                                <td>{{ $item->ShopName }}</td>
                                <td align="right">{{ $item->Bill }}</td>
                                <td>{{ $item->ProductCode }}</td>
                                <td>{{ $item->ProductName }}</td>
                                <td align="right">{{ (int)$item->Qty }}</td>
                                <td align="right">{{ (int)$item->TotalRetailPrice }}</td>
                                <td align="right">{{ (int)$item->Discount }}</td>
                                <td align="right">{{ $item->TotalRetailPrice - $item->Discount }}</td>
                            </tr>
                        @endforeach
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
