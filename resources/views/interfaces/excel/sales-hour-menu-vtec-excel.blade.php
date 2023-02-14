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
        <th>{{ __('Product Code') }}</th>
        <th>{{ __('Product Name') }}</th>
        @for($time = 0; $time <= 23; $time++)
        <th>{{ sprintf("%02d", $time) . '-' . sprintf("%02d", ($time + 1 > 23) ? 0 : $time + 1) }}</th>
        @endfor
    </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $productCode => $item)
        <tr>
            <td>{{ $productCode }}</td>
            <td>{{ $item['ProductName'] }}</td>
            @for($time = 0; $time <= 23; $time++)
            @isset( $item['h'.$time] )
            <td>{{ $item['h'.$time] }}</td>
            @else
            <td>0</td>
            @endisset
            @endfor
        </tr>
        @endforeach
    </tbody>
</table>
