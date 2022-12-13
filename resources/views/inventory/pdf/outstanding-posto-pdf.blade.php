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
                    <td><strong>{{ __('Plant') }} :</strong> {{$data['header']['plant']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>{{ __('Plant') }}</th>
                <th>{{ __('Delivery Number') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Material Code') }}</th>
                <th>{{ __('Material Name') }}</th>
                <th>{{ __('Uom') }}</th>
                <th>{{ __('Qty PO') }}</th>
                <th>{{ __('Qty Outstanding') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item['plant_from'] }}</td>
                <td>{{ $item['sj'] }}</td>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['mat_code'] }}</td>
                <td>{{ $item['mat_desc'] }}</td>
                <td>{{ $item['uom'] }}</td>
                <td align="right">{{ $item['qty'] }}</td>
                <td align="right">{{ $item['qty_out'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
