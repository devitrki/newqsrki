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
                    <td width="33.3%"><strong>Date :</strong> {{$item['header']['date']}}</td>
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
                        <th width="5%">No</th>
                        <th width="32%">Item</th>
                        <th width="7%">Qty</th>
                        <th width="7%">Uom</th>
                        <th width="10%">Time</th>
                        <th width="32%">Remark</th>
                        <th width="17%">PIC</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($item['data'] as $index => $i )
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $i->name }}</td>
                    <td align="right">{{ $i->qty }}</td>
                    <td>{{ $i->uom }}</td>
                    <td>{{ $i->time }}</td>
                    <td>{{ $i->remark }}</td>
                    <td>{{ $i->last_update }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
