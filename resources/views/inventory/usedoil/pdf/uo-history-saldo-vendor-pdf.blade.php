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
                    <td><strong>Vendor :</strong> {{$data['header']['vendor']}}</td>
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
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->nominal, 'Rp. ', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->saldo, 'Rp. ', 0) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
