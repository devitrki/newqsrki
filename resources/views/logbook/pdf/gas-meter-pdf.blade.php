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
                        <th width="5%">{{ __('Date') }}</th>
                        <th width="10%">{{ __('Initial Meter') }}</th>
                        <th width="6%">{{ __('Final Meter') }}</th>
                        <th width="12%">{{ __('Usage') }}</th>
                        <th width="18%">{{ __('Pic') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item )
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                    <td>{{ $item->initial_meter }}</td>
                    <td>{{ $item->final_meter }}</td>
                    <td>{{ $item->usage }}</td>
                    <td>{{ $item->pic }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
