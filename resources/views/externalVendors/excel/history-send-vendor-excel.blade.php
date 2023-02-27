<table>
    <tr>
        <td colspan="7"><strong>Plant :</strong> {{ $data['header']['plant'] }}</td>
    </tr>
    <tr>
        <td colspan="7"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
    <tr>
        <td colspan="7"><strong>Status :</strong> {{ $data['header']['status'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Plant</th>
        <th>Date</th>
        <th>Template Sales</th>
        <th>Target Vendor</th>
        <th>Status</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->plant }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
            <td>{{ $item->template_sales }}</td>
            <td>{{ $item->target_vendor }}</td>
            <td>{{ $item->status_desc }}</td>
            <td>{{ $item->description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
