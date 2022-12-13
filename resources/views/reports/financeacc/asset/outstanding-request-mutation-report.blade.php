@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Outstanding Request Mutation Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ __($header['plant']) }}
                </div>
            </div>
        </div>

        <div class="col-12 p-0 overflow">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Current Step</th>
                        <th scope="col">Plant Sender</th>
                        <th scope="col">Cost Center Sender</th>
                        <th scope="col">Plant Receiver</th>
                        <th scope="col">Cost Center Receiver</th>
                        <th scope="col">Asset Number</th>
                        <th scope="col">Sub Number</th>
                        <th scope="col">Description</th>
                        <th scope="col">Spec / User</th>
                        <th scope="col">Remark</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Uom</th>
                        <th scope="col">Validator</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Approved Date</th>
                        <th scope="col">Confirmation Date</th>
                        <th scope="col">Send Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="Current Step">{{ $item->step_request_desc }}</td>
                            <td data-label="Plant Sender">{{ $item->from_plant_initital . ' ' . $item->from_plant_name }}</td>
                            <td data-label="Cost Center Sender">{{ $item->from_cost_center }}</td>
                            <td data-label="Plant Receiver">{{ $item->to_plant_initital . ' ' . $item->to_plant_name }}</td>
                            <td data-label="Cost Center Receiver">{{ $item->to_cost_center }}</td>
                            <td data-label="Asset Number">{{ $item->number }}</td>
                            <td data-label="Sub Number">{{ $item->number_sub }}</td>
                            <td data-label="Description">{{ $item->description }}</td>
                            <td data-label="Spec / User">{{ $item->spec_user }}</td>
                            <td data-label="Remark">{{ $item->remark }}</td>
                            <td data-label="Qty">{{ $item->qty_mutation }}</td>
                            <td data-label="Uom">{{ $item->uom }}</td>
                            <td data-label="Validator">{{ $item->validator }}</td>
                            <td data-label="Request Date">{{ ($item->date_submit != '' && $item->date_submit != null) ? App\Library\Helper::DateConvertFormat($item->date_submit, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Approved Date">{{ ($item->date_approve_hod != '' && $item->date_approve_hod != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_hod, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Confirmation Date">{{ ($item->date_confirmation_validator != '' && $item->date_confirmation_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Send Date">{{ ($item->date_send != '' && $item->date_send != null) ? App\Library\Helper::DateConvertFormat($item->date_send, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
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
