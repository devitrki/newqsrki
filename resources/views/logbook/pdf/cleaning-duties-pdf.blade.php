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
                    <td width="33.3%"><strong>{{ __('Section') }} :</strong> {{$item['header']['section']}}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <tbody>
                {{-- daily --}}
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%" colspan="2">Daily Task</th>
                        <th width="7%">Opening</th>
                        <th width="7%">Closing</th>
                        <th width="14%" colspan="2">Midnite</th>
                    </tr>
                    @foreach ($item['data']['daily'] as $index => $i )
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td colspan="2">{{ $i->task }}</td>
                        <td>
                            @if($i->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($i->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td colspan="2">
                            @if($i->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    {{-- weekly --}}
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Weekly Task</th>
                        <th width="7%">Day</th>
                        <th width="7%">Opening</th>
                        <th width="7%">Closing</th>
                        <th width="7%">Midnite</th>
                        <th width="7%">Pic</th>
                    </tr>
                    @foreach ($item['data']['weekly'] as $index => $i )
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $i->task }}</td>
                        <td>{{ $i->day }}</td>
                        <td>
                            @if($i->opening == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($i->closing == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>
                            @if($i->midnite == '1')
                            <div style="font-family: DejaVu Sans, sans-serif;color:green;">✔</div>
                            @else
                            <div style="font-family: DejaVu Sans, sans-serif;color:red;">✖</div>
                            @endif
                        </td>
                        <td>{{ $i->pic }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="7">
                            <strong>Catatan:</strong>

                            @isset($item['data']['weekly'][0]->note)
                            {{ $item['data']['weekly'][0]->note }}
                            @endisset
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
