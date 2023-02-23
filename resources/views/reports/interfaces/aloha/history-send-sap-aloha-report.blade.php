@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('History Send SAP Aloha Report') }}</p>
        </div>
        <div class="col-12 p-0">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Store Code</th>
                        <th scope="col">Store</th>
                        <th scope="col">Total Payment</th>
                        <th scope="col">Total Sales</th>
                        <th scope="col">Selisih</th>
                        <th scope="col">Status</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="Date">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd/m/Y') }}</td>
                            <td data-label="Store Code">{{ App\Models\Plant::getCustomerCodeById( $item->plant_id ) }}</td>
                            <td data-label="Store">{{ App\Models\Plant::getShortNameById( $item->plant_id ) }}</td>
                            <td data-label="Total Payment" align="right">{{ App\Library\Helper::convertNumberToInd($item->total_payments, '', 2) }}</td>
                            <td data-label="Total Sales" align="right">{{ App\Library\Helper::convertNumberToInd($item->total_sales, '', 2) }}</td>
                            <td data-label="Selisih" align="right">{{ App\Library\Helper::convertNumberToInd($item->selisih, '', 2) }}</td>
                            <td data-label="Status">{{ ($item->send != 1) ? 'Not Yet' : 'Send' }}</td>
                            <td data-label="Description">{!! $item->description !!}</td>
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
