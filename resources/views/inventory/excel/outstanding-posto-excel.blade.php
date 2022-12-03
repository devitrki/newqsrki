<table>
    <tr>
        <td colspan="6"><strong>Outlet :</strong> {{$data['header']['plant']}}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>{{ __('Plant') }}</th>
        <th>{{ __('Delivery Number') }}</th>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Material Code') }}</th>
        <th>{{ __('Material Name') }}</th>
        <th>{{ __('Uom') }}</th>
        <th>{{ __('Qty PO') }}</th>
        <th>{{ __('Qty Outstanding') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item['plant_from'] }}</td>
                <td>{{ $item['sj'] }}</td>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['mat_code'] }}</td>
                <td>{{ $item['mat_desc'] }}</td>
                <td>{{ $item['uom'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['qty_out'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
