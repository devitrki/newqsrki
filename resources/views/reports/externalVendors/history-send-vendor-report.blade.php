@extends('reports.templates.view.template1')
@section('content')
@if ($count > 0)
<div class="col-12 border-bottom py-1 title">
    <p class="text-center m-0">{{ __('History Send Vendor Report') }}</p>
</div>
<div class="col-12 p-0">
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Plant</th>
                <th scope="col">Date</th>
                <th scope="col">Template Sales</th>
                <th scope="col">Target Vendor</th>
                <th scope="col">Status</th>
                <th scope="col">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
            <tr>
                <td data-label="No">{{ $i+1 }}</td>
                <td data-label="Plant">{{ $item->plant }}</td>
                <td data-label="Date">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
                <td data-label="Template Sales">{{ $item->template_sales }}</td>
                <td data-label="Target Vendor">{{ $item->target_vendor }}</td>
                <td data-label="Status">{{ $item->status_desc }}</td>
                <td data-label="Description">{{ $item->description }}</td>
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
