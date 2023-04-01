@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Plant :</strong> {{ $data['header']['plant'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Month') }} :</strong> {{ $data['header']['month'] }} {{ $data['header']['year'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th width="5%">{{ __('No') }}</th>
                        <th width="10%">{{ __('Date') }}</th>
                        <th width="10%">{{ __('Organik') }}</th>
                        <th width="10%">{{ __('Non Organik') }}</th>
                        <th width="10%">{{ __('Daur Ulang') }}</th>
                        <th width="10%">{{ __('B3') }}</th>
                        <th width="10%">{{ __('PIC') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item )
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                    <td>{{ $item->organik }}</td>
                    <td>{{ $item->non_organik }}</td>
                    <td>{{ $item->daur_ulang }}</td>
                    <td>{{ $item->b3 }}</td>
                    <td>{{ $item->pic }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
