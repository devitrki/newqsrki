@extends('reports.templates.pdf.template-logbook')
@section('style')
    <style>
        main .body table th {
            height: 1rem;
        }
        main .body table td {
            height: 3.3rem;
        }
    </style>
@endsection
@section('title')
{{ $title }}
@endsection
@section('content')
    @foreach($data['items'] as $item)
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Outlet :</strong> {{$item['header']['plant']}}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{$item['header']['date']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            @foreach($item['data'] as $d)
                <table>
                    <tr>
                        <th width="40%" colspan="2"><strong>{{ $d['shift'] }} Briefing</strong></th>
                        <th width="60%" align="center" colspan="5"><strong>Duty Roster</strong></th>
                    </tr>
                    @foreach($d['rows'] as $row)
                    <tr>
                        <td width="15%"><strong>{{ $row['col1'] }}</strong></td>
                        <td width="15%">{{ $row['col2'] }}</td>
                        <td width="12%"><strong>{{ $row['col3'] }}</strong></td>
                        <td width="12%">{{ $row['col4'] }}</td>
                        <td width="12%">{{ $row['col5'] }}</td>
                        <td width="12%">{{ $row['col6'] }}</td>
                        <td width="12%">{{ $row['col7'] }}</td>
                    </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
    </div>
    @endforeach
@endsection
