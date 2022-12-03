<table>
    <tr>
        <td><strong>{{ $plant['type'] }} :</strong></td>
        <td colspan="7">{{ $plant['code'] . '-' . $plant['name'] }}</td>
    </tr>
    <tr>
        <td><strong>Cost Center :</strong></td>
        <td colspan="7">{{ $assetSoPlant['head']->cost_center_code . '-' . $assetSoPlant['head']->cost_center }}</td>
    </tr>
    <tr>
        <td><strong>Periode Asset SO :</strong></td>
        <td colspan="7">{{ $assetSoPlant['label'] . ' ' . $assetSoPlant['head']->year }}</td>
    </tr>
    <tr>
        <td><strong>Upload Code :</strong></td>
        <td colspan="7">{{ $assetSoPlant['head']->upload_code }}</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td><strong>Selisih Lebih :</strong></td>
        <td colspan="7" rowspan="3">{{ $assetSoPlant['head']->note }}</td>
    </tr>
    <tr></tr>
    <tr></tr>
</table>

<table>
    <thead>
        <tr>
            <th>Asset Number</th>
            <th>Sub Number</th>
            <th>Description</th>
            <th>Spec / User</th>
            <th>QTY SO</th>
            <th>UOM</th>
            <th>Remark</th>
            <th>Remark SO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assetSoPlant['detail'] as $item)
        <tr>
            <td>{{ $item->number }}</td>
            <td>{{ $item->number_sub }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->spec_user }}</td>
            <td>{{ $item->qty_so }}</td>
            <td>{{ $item->uom }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->remark_so }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
