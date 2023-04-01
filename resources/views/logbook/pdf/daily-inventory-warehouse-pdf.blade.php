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
                        <th rowspan="2" width="24%">Product Name</th>
                        <th rowspan="2" width="4%">OUM</th>
                        <th rowspan="2" width="6%">Frekuensi</th>
                        <th rowspan="2" width="6%">Opening Stock</th>
                        <th colspan="4">Stock In</th>
                        <th colspan="4">Stock Out</th>
                        <th rowspan="2" width="6%">Closing Stock</th>
                        <th rowspan="2" width="6%">Note</th>
                    </tr>
                    <tr>
                        <th width="6%">GR Plant</th>
                        <th width="6%">DC</th>
                        <th width="6%">Vendor</th>
                        <th width="6%">Section</th>
                        <th width="6%">GI Plant</th>
                        <th width="6%">DC</th>
                        <th width="6%">Vendor</th>
                        <th width="6%">Section</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($item['data'] as $i )
                <tr>
                    <td>{{$i->product_name}}</td>
                    <td>{{$i->uom}}</td>
                    <td>{{$i->frekuensi}}</td>
                    <td align="right">{{$i->stock_opening}}</td>
                    <td align="right">{{$i->stock_in_gr_plant}}</td>
                    <td align="right">{{$i->stock_in_dc}}</td>
                    <td align="right">{{$i->stock_in_vendor}}</td>
                    <td align="right">{{$i->stock_in_section}}</td>
                    <td align="right">{{$i->stock_out_gi_plant}}</td>
                    <td align="right">{{$i->stock_out_dc}}</td>
                    <td align="right">{{$i->stock_out_vendor}}</td>
                    <td align="right">{{$i->stock_out_section}}</td>
                    <td align="right">{{$i->stock_closing}}</td>
                    <td>{{$i->note}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
