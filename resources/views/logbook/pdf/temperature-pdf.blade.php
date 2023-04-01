@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Plant : </strong>{{ $data['header']['plant'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('From Date') }} : </strong>{{ $data['header']['date_from'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Until Date') }} : </strong>{{ $data['header']['date_until'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Storage') }} : </strong>{{ $data['header']['storage'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">{{ __('Date') }}</th>
                        @foreach($data['header']['range_check_temp'] as $is => $temp)
                        <th width="10%">{{ $temp }}</th>
                        @endforeach
                        <th width="25%">{{ __('Note') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item )
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                    @foreach($data['header']['range_check_temp'] as $is => $temp)
                    <td>{{ $item->{'temp_'.($is+1)} }}</td>
                    @endforeach
                    <td>{{ $item->note }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
