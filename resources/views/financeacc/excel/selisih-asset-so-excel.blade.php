<table>
    <tr>
        <td colspan="18"><strong>Plant :</strong> {{ __($data['header']['plant']) }}</td>
    </tr>
    <tr>
        <td colspan="18"><strong>Periode :</strong> {{ __($data['header']['periode']) }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Plant</th>
            <th>Cost Center</th>
            <th>Cost Center Code</th>
            <th>Asset Number</th>
            <th>Sub Number</th>
            <th>Description</th>
            <th>Spec / User</th>
            <th>QTY SO</th>
            <th>QTY Web</th>
            <th>QTY Selisih</th>
            <th>UOM</th>
            <th>Remark</th>
            <th>Remark SO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $item->initital . ' ' . $item->short_name }}</td>
            <td>{{ $item->cost_center }}</td>
            <td>{{ $item->cost_center_code }}</td>
            <td>{{ $item->number }}</td>
            <td>{{ $item->number_sub }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->spec_user }}</td>
            <td>{{ $item->qty_so }}</td>
            <td>{{ $item->qty_web }}</td>
            <td>{{ $item->qty_selisih }}</td>
            <td>{{ $item->uom }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->remark_so }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
