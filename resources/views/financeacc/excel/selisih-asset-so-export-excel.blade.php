<table>
    <tr>
        <td><strong>Periode Asset SO :</strong></td>
        <td colspan="9">{{ $periode }}</td>
    </tr>
    <tr>
        <td><strong>Plant :</strong></td>
        <td colspan="9">{{ $plant }}</td>
    </tr>
    <tr>
        <td><strong>Cost Center :</strong></td>
        <td colspan="9">{{ $costcenter }}</td>
    </tr>
    <tr>
        <td><strong>Selisih Lebih :</strong></td>
        <td colspan="9">{{ $note }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Number</th>
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
        @foreach ($data as $item)
        <tr>
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
