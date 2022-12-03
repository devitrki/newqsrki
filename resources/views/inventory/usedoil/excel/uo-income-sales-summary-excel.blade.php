<table>
    <tr>
        <td colspan="4"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>{{ __('Plant Code') }}</th>
        <th>{{ __('Plant Name') }}</th>
        <th>{{ __('Total Sales') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ App\Models\Plant::getCodeById($item->plant_id_sender) }}</td>
                <td>{{ App\Models\Plant::getShortNameById($item->plant_id_sender) }}</td>
                <td>{{ $item->total_sales }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
