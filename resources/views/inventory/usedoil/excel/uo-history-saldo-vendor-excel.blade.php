@foreach($data['datas'] as $data)
<table>
    <tr>
        <td colspan="11"><strong>Vendor :</strong> {{ $data['header']['vendor'] }}</td>
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
        <th>{{ __('Transaction Type') }}</th>
        <th>{{ __('Nominal') }}</th>
        <th>{{ __('Saldo') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->nominal }}</td>
            <td>{{ $item->saldo }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
