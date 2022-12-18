<table>
    <tr>
        <td colspan="27"><strong>Store :</strong> {{ $data['header']['store'] }}</td>
    </tr>
    <tr>
        <td colspan="27"><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Reciept Number') }}</th>
        <th>{{ __('Total Payment') }}</th>
        <th>{{ __('Void Time') }}</th>
        <th>{{ __('Void Staff') }}</th>
        <th>{{ __('Void Reason') }}</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $item)
        <tr>
            <td>{{ App\Library\Helper::DateConvertFormat($item->SaleDate, 'Y-m-d', 'd/m/Y') }}</td>
            <td>{{ $item->ReceiptNumber }}</td>
            <td>{{ $item->PayAmount }}</td>
            <td>{{ $item->time }}</td>
            <td>{{ $item->VoidStaff }}</td>
            <td>{{ $item->VoidReason }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
