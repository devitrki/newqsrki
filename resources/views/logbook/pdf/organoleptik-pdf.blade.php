@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>{{ $data['header']['plant'] }}</strong></td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" width="3%">No</th>
                        <th rowspan="2" width="10%">Date</th>
                        <th rowspan="2" width="15%">Product</th>
                        <th rowspan="2" width="8%">Code</th>
                        <th rowspan="2" width="8%">Time</th>
                        <th colspan="4" width="40%">Organoleptik</th>
                        <th rowspan="2" width="10%">PIC</th>
                    </tr>
                    <tr>
                        <th width="10%">Taste</th>
                        <th width="10%">Aroma</th>
                        <th width="10%">Texture</th>
                        <th width="10%">Color</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item )
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                    <td>{{ $item->product }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->time }}</td>
                    <td>{{ $item->taste }}</td>
                    <td>{{ $item->aroma }}</td>
                    <td>{{ $item->texture }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->pic }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
