<table>
    <tr>
        <td colspan="4"><strong>Store :</strong> {{ $data['header']['store'] }}</td>
    </tr>
    <tr>
        <td colspan="4"><strong>Date :</strong> {{ $data['header']['date'] }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>{{ __('Payment | Promotion') }}</th>
            <th>{{ __('Qty') }}</th>
            <th>{{ __('Amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->PayTypeName }}</td>
            <td>{{ $item->TotalQty }}</td>
            <td>{{ $item->PayAmount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
