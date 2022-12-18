@if ($data['status'])
    <table>
        <thead>
        <tr>
            <th>{{ __('DATE') }}</th>
            <th>{{ __('STORE CODE') }}</th>
            <th>{{ __('STORE NAME') }}</th>
            <th>{{ __('POS') }}</th>
            @foreach($data['headers'] as $header)
            <th>{{ $header }}</th>
            <th>{{ __('QTY') }}</th>
            @endforeach
            <th>{{ __('TOTAL PAYMENT') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($data['items'] as $item)
            <tr>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['store_code'] }}</td>
                <td>{{ $item['store_name'] }}</td>
                <td>{{ $item['pos'] }}</td>

                @foreach($data['headers'] as $header)
                <td align="right">{{ $item[$header] }}</td>
                <td align="right">{{ $item['qty'.$header] }}</td>
                @endforeach

                <td align="right">{{ $item['total_payment'] }}</td>
            </tr>
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
