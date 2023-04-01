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
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Task</th>
                        <th width="7%">Opening</th>
                        <th width="7%">CLosing</th>
                        <th width="7%">Midnite</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($item['data'] as $index => $i )
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $i->task }}</td>
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
                </tr>
                @endforeach
                <tr>
                    <td colspan="5">
                        <strong>Catatan:</strong>

                        @isset($item['data'][0]->note)
                        {{ $item['data'][0]->note }}
                        @endisset
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
