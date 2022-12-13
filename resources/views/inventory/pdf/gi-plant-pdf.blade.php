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
                    <td><strong>Plant :</strong> {{$data['header']['plant']}}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>GI Number</th>
                <th>POSTO Number</th>
                <th>Date</th>
                <th>Material Code</th>
                <th>Material Description</th>
                <th>QTY GI</th>
                <th>QTY GR</th>
                <th>Outstanding GR</th>
                <th>UOM</th>
                <th>Receiving Plant</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->document_number }}</td>
                <td>{{ $item->document_posto }}</td>
                <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
                <td>{{ $item->material_code }}</td>
                <td>{{ $item->material_desc }}</td>
                <td align="right">{{ $item->gi_qty }}</td>
                <td align="right">{{ $item->gr_qty }}</td>
                <td align="right">{{ $item->gr_outstanding }}</td>
                <td>{{ $item->uom }}</td>
                <td>{{ $item->initital . ' ' .$item->short_name }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
