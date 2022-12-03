<table>
    <tr>
        <td colspan="11"><strong>Outlet :</strong> {{$data['header']['plant']}}</td>
    </tr>
    <tr>
        <td colspan="11"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>GI Number</th>
        <th>POSTO Number</th>
        <th>Date</th>
        <th>Material Code</th>
        <th>Material Description</th>
        <th>QTY GI</th>
        <th>QTY GR</th>
        <th>Outstanding GR</th>
        <th>UOM</th>
        <th>Receiving Plant</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->document_number }}</td>
            <td>{{ $item->document_posto }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
            <td>{{ $item->material_code }}</td>
            <td>{{ $item->material_desc }}</td>
            <td align="right">{{ $item->gi_qty }}</td>
            <td align="right">{{ $item->gr_qty }}</td>
            <td align="right">{{ $item->gr_outstanding }}</td>
            <td>{{ $item->uom }}</td>
            <td>{{ $item->initital . ' ' .$item->short_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>