@foreach($data['datas'] as $data)
<table>
    <tr>
        <td colspan="11"><strong>Plant :</strong> {{ $data['header']['plant'] }}</td>
    </tr>
    <tr>
        <td colspan="11"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Plant Code') }}</th>
        <th>{{ __('Plant Name') }}</th>
        <th>{{ __('Vendor') }}</th>
        <th>{{ __('Document Number') }}</th>
        <th>{{ __('Material') }}</th>
        <th>{{ __('QTY') }}</th>
        <th>{{ __('Price') }}</th>
        <th>{{ __('Total') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->initital . ' ' . $item->short_name }}</td>
            <td>{{ $item->vendor_name }}</td>
            <td>{{ $item->document_number }}</td>
            <td>{{ $item->material_name }}</td>
            <td>{{ abs($item->qty) }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ round(abs($item->qty) * $item->price, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
