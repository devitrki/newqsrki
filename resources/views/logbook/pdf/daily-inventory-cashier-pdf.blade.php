@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    @foreach($data['items'] as $item)
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td width="33.3%"><strong>Outlet :</strong> {{$item['header']['plant']}}</td>
                    <td width="33.3%"><strong>Date :</strong> {{$item['header']['date']}}</td></td>
                    <td width="33.3%">
                        <strong>MOD :</strong>
                        @isset($item['header']['appReview']->mod_pic)
                            {{ $item['header']['appReview']->mod_pic }}
                        @else
                            -
                        @endisset
                    </td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th width="35%">Product Name</th>
                        <th width="5%">UOM</th>
                        <th width="10%">Frekuensi</th>
                        <th width="10%">Stock Opening </th>
                        <th width="10%">Stock In</th>
                        <th width="10%">Stock Out</th>
                        <th width="10%">Stock Closing</th>
                        <th width="10%">Note</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($item['data'] as $i )
                <tr>
                    <td>{{ $i->product_name }}</td>
                    <td>{{ $i->uom }}</td>
                    <td>{{ $i->frekuensi }}</td>
                    <td align="right">{{ $i->stock_opening }}</td>
                    <td align="right">{{ $i->stock_in }}</td>
                    <td align="right">{{ $i->stock_out }}</td>
                    <td align="right">{{ $i->stock_closing }}</td>
                    <td>{{ $i->note }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection

