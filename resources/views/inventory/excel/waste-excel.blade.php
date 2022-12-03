@foreach($data['datas'] as $data)
<table>
    <tr>
        <td colspan="5"><strong>Plant :</strong> {{ $data['header']['plant_code'] . ' - ' . $data['header']['plant_name'] }}</td>
    </tr>
    <tr>
        <td colspan="5"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>{{ __('Plant Code') }}</th>
        <th>{{ __('Plant Name') }}</th>
        <th>{{ __('Material Code') }}</th>
        <th>{{ __('Material Name') }}</th>
        <th>{{ __('QTY') }}</th>
        <th>{{ __('Uom') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $data['header']['plant_code'] }}</td>
            <td>{{ $data['header']['plant_name'] }}</td>
            <td>{{ $item->material_code }}</td>
            <td>{{ $item->material_name }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ $item->uom }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
