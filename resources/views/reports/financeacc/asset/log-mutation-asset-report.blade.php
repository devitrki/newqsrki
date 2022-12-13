@extends('reports.templates.view.template1')
@section('content')
    @if ($count > 0)
        <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Log Asset Transfer Report') }}</p>
        </div>
        <div class="col-12">
            <div class="row head-item-row">
                <div class="col-12 head-item">
                    <strong>Plant :</strong> {{ __($header['plant']) }}
                </div>
                <div class="col-12 head-item">
                    <strong>From Date :</strong> {{ $header['date_from'] }}
                </div>
                <div class="col-12 head-item">
                    <strong>Until Date :</strong> {{ $header['date_until'] }}
                </div>
            </div>
        </div>

        <div class="col-12 p-0 overflow">
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Status</th>
                        <th scope="col">Asset Number</th>
                        <th scope="col">Sub Number</th>
                        <th scope="col">Description</th>
                        <th scope="col">Spec / User</th>
                        <th scope="col">Remark</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Uom</th>
                        <th scope="col">Plant Sender</th>
                        <th scope="col">Cost Center Sender</th>
                        <th scope="col">PIC Sender</th>
                        <th scope="col">Condition Send</th>
                        <th scope="col">Plant Receiver</th>
                        <th scope="col">Cost Center Receiver</th>
                        <th scope="col">PIC Receiver</th>
                        <th scope="col">Condition Receive</th>
                        <th scope="col">Requestor</th>
                        <th scope="col">Approver 1</th>
                        <th scope="col">Validator</th>
                        <th scope="col">Approver 2</th>
                        <th scope="col">Est Transfer Date</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Cancel Request Date</th>
                        <th scope="col">Approver 1 Date</th>
                        <th scope="col">UnApprove Approver 1 Date</th>
                        <th scope="col">Confirm Validator Date</th>
                        <th scope="col">Reject Validator Date</th>
                        <th scope="col">Approver 2 Date</th>
                        <th scope="col">UnApprove Approver 2 Date</th>
                        <th scope="col">Confirm Sender Date</th>
                        <th scope="col">Reject Sender Date</th>
                        <th scope="col">Accepted Receiver Date</th>
                        <th scope="col">Reject Receiver Date</th>
                        <th scope="col">Note Request</th>
                        <th scope="col">Reason Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        <tr>
                            <td data-label="No">{{ $i+1 }}</td>
                            <td data-label="Status">{{ $item->status_mutation_desc }}</td>
                            <td data-label="Asset Number">{{ $item->number }}</td>
                            <td data-label="Sub Number">{{ $item->number_sub }}</td>
                            <td data-label="Description">{{ $item->description }}</td>
                            <td data-label="Spec / User">{{ $item->spec_user }}</td>
                            <td data-label="Remark">{{ $item->remark }}</td>
                            <td data-label="Qty" align="right">{{ $item->qty_mutation }}</td>
                            <td data-label="Uom">{{ $item->uom }}</td>
                            <td data-label="Plant Sender">{{ $item->from_plant_initital . ' ' . $item->from_plant_name }}</td>
                            <td data-label="Cost Center Sender">{{ $item->from_cost_center . ' - ' . $item->from_cost_center_code }}</td>
                            <td data-label="PIC Sender">{{ $item->pic_sender }}</td>
                            <td data-label="Condition Send">{{ $item->condition_send }}</td>
                            <td data-label="Plant Receiver">{{ $item->to_plant_initital . ' ' . $item->to_plant_name }}</td>
                            <td data-label="Cost Center Receiver">{{ $item->to_cost_center . ' - ' . $item->to_cost_center_code }}</td>
                            <td data-label="PIC Receiver">{{ $item->pic_receiver }}</td>
                            <td data-label="Condition Receive">{{ $item->condition_receive }}</td>
                            <td data-label="Requestor">{{ App\Models\User::getNameById($item->user_id) . ' (' . $item->requestor . ')' }}</td>
                            <td data-label="Approver 1">{{ App\Models\User::getNameById($item->level_request_first_id) . ' (' . $item->level_request_first . ')' }}</td>
                            <td data-label="Validator">{{ App\Models\Financeacc\AssetValidator::getNameById($item->asset_validator_id) }}</td>
                            <td data-label="Approver 2">{{ App\Models\User::getNameById($item->level_request_second_id) . ' (' . $item->level_request_second . ')' }}</td>
                            <td data-label="Est Transfer Date">{{ ($item->date_send_est != '' && $item->date_send_est != null) ? App\Library\Helper::DateConvertFormat($item->date_send_est, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Request Date">{{ ($item->date_request != '' && $item->date_request != null) ? App\Library\Helper::DateConvertFormat($item->date_request, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Cancel Request Date">{{ ($item->date_cancel_request != '' && $item->date_cancel_request != null) ? App\Library\Helper::DateConvertFormat($item->date_cancel_request, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Approver 1 Date">{{ ($item->date_approve_first != '' && $item->date_approve_first != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_first, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="UnApprove Approver 1 Date">{{ ($item->date_unapprove_first != '' && $item->date_unapprove_first != null) ? App\Library\Helper::DateConvertFormat($item->date_unapprove_first, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Confirm Validator Date">{{ ($item->date_confirmation_validator != '' && $item->date_confirmation_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Reject Validator Date">{{ ($item->date_reject_validator != '' && $item->date_reject_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Approver 2 Date">{{ ($item->date_approve_second != '' && $item->date_approve_second != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_second, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="UnApprove Approver 2 Date">{{ ($item->date_unapprove_second != '' && $item->date_unapprove_second != null) ? App\Library\Helper::DateConvertFormat($item->date_unapprove_second, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Confirm Sender Date">{{ ($item->date_confirmation_sender != '' && $item->date_confirmation_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Reject Sender Date">{{ ($item->date_reject_sender != '' && $item->date_reject_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Accepted Receiver Date">{{ ($item->date_confirmation_sender != '' && $item->date_confirmation_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Reject Receiver Date">{{ ($item->date_reject_receiver != '' && $item->date_reject_receiver != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_receiver, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
                            <td data-label="Note Request">{{ $item->note_request }}</td>
                            <td data-label="Reason Rejected">{{ $item->reason_rejected }}</td>
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
