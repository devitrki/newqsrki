@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td width="50%"><strong>{{ __('Month') }} :</strong> {{ $data['header']['month'] }} {{ $data['header']['year'] }}</td>
                    <td width="50%" align="right"><strong>{{ $data['header']['plant'] }}</strong></td>
                </tr>
                <tr>
                    <td><strong>{{ __('Item') }} :</strong> {{ $data['header']['materialName'] }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('Uom') }} :</strong> {{ $data['header']['materialUom'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">{{ __('Date') }}</th>
                        <th rowspan="2" width="10%">{{ __('No PO') }}</th>
                        <th rowspan="2" width="6%">{{ __('Initial Stock') }}</th>
                        <th colspan="2" width="12%">{{ __('IN') }}</th>
                        <th colspan="3" width="18%">{{ __('OUT') }}</th>
                        <th rowspan="2" width="6%">{{ __('Last Stock') }}</th>
                        <th rowspan="2" width="15%">{{ __('Description') }}</th>
                    </tr>
                    <tr>
                        <th width="6%">GR</th>
                        <th width="6%">TF IN</th>
                        <th width="6%">Used</th>
                        <th width="6%">Waste</th>
                        <th width="6%">TF GI</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $item )
                <tr>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'j') }}</td>
                    <td>{{ $item->no_po }}</td>
                    <td>{{ $item->stock_initial }}</td>
                    <td align="right">{{ $item->stock_in_gr }}</td>
                    <td align="right">{{ $item->stock_in_tf }}</td>
                    <td align="right">{{ $item->stock_out_used }}</td>
                    <td align="right">{{ $item->stock_out_waste }}</td>
                    <td align="right">{{ $item->stock_out_tf }}</td>
                    <td align="right">{{ $item->stock_last }}</td>
                    <td>{{ $item->description }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
