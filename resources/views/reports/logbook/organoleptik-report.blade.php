@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Reception Material / Product Outlet') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 col-md-4 head-item">
                <strong>Outlet :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>From Date :</strong> {{ __($header['date_from']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Until Date :</strong> {{ __($header['date_until']) }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Date</th>
                    <th scope="col">Product</th>
                    <th scope="col">Code</th>
                    <th scope="col">Time</th>
                    <th scope="col">Taste</th>
                    <th scope="col">Aroma</th>
                    <th scope="col">Texture</th>
                    <th scope="col">Color</th>
                    <th scope="col">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Date">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                        <td data-label="Product">{{ $item->product }}</td>
                        <td data-label="Code">{{ $item->code }}</td>
                        <td data-label="Time">{{ $item->time }}</td>
                        <td data-label="Taste">{{ $item->taste }}</td>
                        <td data-label="Aroma">{{ $item->aroma }}</td>
                        <td data-label="Texture">{{ $item->texture }}</td>
                        <td data-label="Color">{{ $item->color }}</td>
                        <td data-label="PIC">{{ $item->pic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
