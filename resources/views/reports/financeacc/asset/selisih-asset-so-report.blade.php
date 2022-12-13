@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Asset SO Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ __($header['plant']) }}
                </div>
                <div class="col-12 head-item">
                    <strong>Periode :</strong> {{ $header['periode'] }}
                </div>
            </div>
        </div>

        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">Plant</th>
                        <th scope="col">Cost Center</th>
                        <th scope="col">Cost Center Code</th>
                        <th scope="col">Asset Number</th>
                        <th scope="col">Sub Number</th>
                        <th scope="col">Description</th>
                        <th scope="col">Spec / User</th>
                        <th scope="col">QTY SO</th>
                        <th scope="col">QTY Web</th>
                        <th scope="col">QTY Selisih</th>
                        <th scope="col">UOM</th>
                        <th scope="col">Remark</th>
                        <th scope="col">Remark SO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="Plant">{{ $item->initital . ' ' . $item->short_name }}</td>
                            <td data-label="Cost Center">{{ $item->cost_center }}</td>
                            <td data-label="Cost Center Code">{{ $item->cost_center_code }}</td>
                            <td data-label="Asset Number">{{ $item->number }}</td>
                            <td data-label="Sub Number">{{ $item->number_sub }}</td>
                            <td data-label="Description">{{ $item->description }}</td>
                            <td data-label="Spec / User">{{ $item->spec_user }}</td>
                            <td data-label="QTY SO">{{ $item->qty_so }}</td>
                            <td data-label="QTY Web">{{ $item->qty_web }}</td>
                            <td data-label="QTY Selisih">{{ $item->qty_selisih }}</td>
                            <td data-label="UOM">{{ $item->uom }}</td>
                            <td data-label="Remark">{{ $item->remark }}</td>
                            <td data-label="Remark SO">{{ $item->remark_so }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
        </div>
    @endif
@endsection
