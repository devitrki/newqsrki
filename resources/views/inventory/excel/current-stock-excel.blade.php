<table>
    <tr>
        <td colspan="6"><strong>Outlet :</strong> {{$data['header']['plant']}}</td>
    </tr>
    <tr>
        <td colspan="6"><strong>{{ __('Material Type') }} :</strong> {{ $data['header']['material_type'] }}</td>
    </tr>
    <tr>
        <td colspan="6"><strong>{{ __('Stock Date') }} :</strong> {{ date('d-m-Y H:i:s') }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>{{ __('Plant') }}</th>
        <th>{{ __('Material Type') }}</th>
        <th>{{ __('Material Code') }}</th>
        <th>{{ __('Material Name') }}</th>
        <th>{{ __('Qty') }}</th>
        <th>{{ __('Uom') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item['plant'] }}</td>
                <td>{{ $item['material_type'] }}</td>
                <td>{{ $item['material_code'] }}</td>
                <td>{{ $item['material_desc'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['uom'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
