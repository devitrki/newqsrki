@extends('reports.templates.pdf.template-multiple')
@section('title')
{{ $title }}
@endsection
@section('content')
    @foreach($data['datas'] as $data)
    <div class="report">
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
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Document Number') }}</th>
                    <th>{{ __('Material') }}</th>
                    <th>{{ __('QTY') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ date("d-m-Y", strtotime($item->date)) }}</td>
                    <td>{{ $item->document_number }}</td>
                    <td>{{ $item->material_name }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd(abs($item->qty), '', 2) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->price, '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd(round(abs($item->qty) * $item->price, 0), '', 0) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
