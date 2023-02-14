<table>
    <tr>
        <td colspan="11"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ Illuminate\Support\Str::of($item->description)->replace('</br>', ', ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
