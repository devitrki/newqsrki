<table>
    <thead>
    <tr>
        <th>{{ __('DATE') }}</th>
        <th>{{ __('STORE CODE') }}</th>
        <th>{{ __('STORE NAME') }}</th>
        @foreach($data['headers'] as $header)
        <th scope="col">{{ $header }}</th>
        @endforeach
        <th>{{ __('TOTAL PAYMENT') }}</th>
        <th>{{ __('TOTAL SALES') }}</th>
        <th>{{ __('SELISIH') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $item)
        <tr>
            <td>{{ $item['date'] }}</td>
            <td>{{ $item['store_code'] }}</td>
            <td>{{ $item['store_name'] }}</td>

            @foreach($data['headers'] as $header)
            <td align="right">{{ $item[$header] }}</td>
            @endforeach

            <td align="right">{{ $item['total_payment'] }}</td>
            <td align="right">{{ $item['total_sales'] }}</td>
            <td align="right">{{ $item['selisih'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
