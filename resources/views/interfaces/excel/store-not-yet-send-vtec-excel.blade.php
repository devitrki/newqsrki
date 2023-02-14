<table>
    <tr>
        <td colspan="4"><strong>Date :</strong> {{ $data['header']['date'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>AM</th>
        <th>Store Code</th>
        <th>Store Name</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item['am'] }}</td>
            <td>{{ $item['store_code'] }}</td>
            <td>{{ $item['store_name'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
