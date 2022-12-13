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
                <th>GR Number</th>
                <th>GI Number</th>
                <th>PO Number</th>
                <th>Issuing Plant</th>
                <th>Receive Date</th>
                <th>Material Code</th>
                <th>Material Description</th>
                <th>QTY PO</th>
                <th>QTY Remaining PO</th>
                <th>QTY GR</th>
                <th>QTY Remaining</th>
                <th>UOM</th>
                <th>Recepient</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->document_number }}</td>
                <td>{{ $item->delivery_number }}</td>
                <td>{{ $item->posto_number }}</td>
                <td>{{ $item->initital . ' ' .$item->short_name }}</td>
                <td>{{ App\Library\Helper::DateConvertFormat($item->receive_date, 'Y-m-d', 'd/m/Y') }}</td>
                <td>{{ $item->material_code }}</td>
                <td>{{ $item->material_desc }}</td>
                <td align="right">{{ round($item->qty_po, 3) }}</td>
                <td align="right">{{ round($item->qty_b4_gr, 3) }}</td>
                <td align="right">{{ round($item->qty_gr, 3) }}</td>
                <td align="right">{{ round($item->qty_remaining, 3) }}</td>
                <td>{{ $item->uom }}</td>
                <td>{{ $item->recepient }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
