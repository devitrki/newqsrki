@extends('reports.templates.pdf.template1')
@section('content')
    <div class="report">
        <div class="header">
            <table>
                <tr>
                    <td class="logo"><img src="{{ asset( 'images/logo/rki.png' ) }}" alt="Richeese Kuliner Indonesia"></td>
                    <td class="title"><h1>{{ $title }}</h1></td>
                </tr>
            </table>
        </div>
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Plant :</strong> {{ $data['header']['plant'] }}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
                </tr>
                <tr>
                    <td><strong>Status :</strong> {{ $data['header']['status'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>Plant</th>
                <th>Date</th>
                <th>FTP Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->plant }}</td>
                <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
                <td>{{ $item->name }}</td>
                <td align="right">{{ $item->amount }}</td>
                <td>{{ $item->status_desc }}</td>
                <td>{{ $item->description }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
