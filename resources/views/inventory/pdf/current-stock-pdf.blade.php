@extends('reports.templates.pdf.template1')
@section('content')
    <div class="report">
        <div class="header">
            <table>
                <tr>
                    <td class="logo"><img src="{{ public_path( 'images/logo/rki.png' ) }}" alt="Richeese Kuliner Indonesia"></td>
                    <td class="title"><h1>{{ $title }}</h1></td>
                </tr>
            </table>
        </div>
        <div class="desc">
            <table>
                <tr>
                    <td><strong>{{ __('Plant') }} :</strong> {{$data['header']['plant']}}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Material Type') }} :</strong> {{ $data['header']['material_type'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Stock Date') }} :</strong> {{ date('d-m-Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>{{ __('Plant') }}</th>
                <th>{{ __('Material Type') }}</th>
                <th>{{ __('Material Code') }}</th>
                <th>{{ __('Material Name') }}</th>
                <th>{{ __('Qty') }}</th>
                <th>{{ __('Uom') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item['plant'] }}</td>
                <td>{{ $item['material_type'] }}</td>
                <td>{{ $item['material_code'] }}</td>
                <td>{{ $item['material_desc'] }}</td>
                <td align="right">{{ App\Library\Helper::convertNumberToInd($item['qty'], '', 3) }}</td>
                <td>{{ $item['uom'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
