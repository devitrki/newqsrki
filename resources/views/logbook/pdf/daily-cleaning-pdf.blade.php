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
                    <td width="33.3%"><strong>{{ __('Date') }} :</strong> {{$item['header']['date']}}</td>
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
                        <th width="25%">Daily Task</th>
                        <th width="7%">Section</th>
                        <th width="7%">Frekuensi</th>
                        @foreach($item['header']['shifts'] as $s)
                        <th width="5%">{{ $s }}</th>
                        @endforeach
                        <th width="16%">PIC</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($item['data'] as $index => $i )
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $i->task }}</td>
                    <td>{{ $i->section }}</td>
                    <td>{{ $i->frekuensi }}</td>
                    @foreach($item['header']['shifts'] as $is => $s)
                    <td>
                        @if($i->{'checklis_'.($is + 1)} == '1')
                        <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                        @else
                        <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                        @endif
                    </td>
                    @endforeach
                    <td>{{ $i->pic }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
