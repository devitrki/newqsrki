@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Outlet :</strong> {{$data['header']['plant']}}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Date') }} :</strong> {{$data['header']['date']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <tbody>
                @foreach($data['items'] as $idx => $item)
                    <tr>
                        <td colspan="9" align="center">
                            <strong>
                                TOILET CHECKLIST
                                @if($item['header']['shift'] == '1')
                                OPENING
                                @elseif($item['header']['shift'] == '2')
                                CLOSING
                                @else
                                MIDNITE
                                @endif
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th width="25%">Task</th>
                        @foreach($item['header']['shifts'] as $s)
                        <th width="7%">{{ $s }}</th>
                        @endforeach
                    </tr>
                    @foreach ($item['data'] as $index => $i )
                    <tr>
                        <td>{{ $i->task }}</td>
                        @foreach($item['header']['shifts'] as $is => $s)
                        <td>
                            @if($i->{'checklis_'.($is + 1)} == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @if($idx < 2)
                    <tr>
                        <td colspan="9" style="border-left:0px solid #FFF;border-right:0px solid #FFF;color:#FFF;">l</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
