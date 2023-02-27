<table>
    <thead>
        <tr>
            @foreach ($fields as $field)
            <th>{{ $field }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            @foreach ($data as $trans)
                @if (is_bool($trans))
                @php
                    $trans = $trans ? 'true' : 'false';
                @endphp
                <td>{{ $trans }}</td>
                @else
                <td>{{ $trans }}</td>
                @endif
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
