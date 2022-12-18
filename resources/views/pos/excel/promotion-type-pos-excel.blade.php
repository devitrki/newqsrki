@if ($data['status'])
    <table>
        <tr>
            <td colspan="9"><strong>Date From :</strong> {{ $data['headers']['date_from'] }}</td>
        </tr>
        <tr>
            <td colspan="9"><strong>Date Until :</strong> {{ $data['headers']['date_until'] }}</td>
        </tr>
    </table>
    <table>
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
            @foreach ($data['items'] as $listItems)
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
@else
    <table>
        <thead>
            <tr>
                <th>{{ __($data['message']) }}</th>
            </tr>
        </thead>
    </table>
@endif
