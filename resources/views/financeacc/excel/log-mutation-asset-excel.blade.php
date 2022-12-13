<table>
    <tr>
        <td colspan="18"><strong>Plant :</strong> {{ __($data['header']['plant']) }}</td>
    </tr>
    <tr>
        <td colspan="18"><strong>From Date :</strong> {{ __($data['header']['date_from']) }}</td>
    </tr>
    <tr>
        <td colspan="18"><strong>Until Date :</strong> {{ __($data['header']['date_until']) }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Status</th>
            <th>Asset Number</th>
            <th>Sub Number</th>
            <th>Description</th>
            <th>Spec / User</th>
            <th>Remark</th>
            <th>Qty</th>
            <th>Uom</th>
            <th>Plant Sender</th>
            <th>Cost Center Sender</th>
            <th>PIC Sender</th>
            <th>Condition Send</th>
            <th>Plant Receiver</th>
            <th>Cost Center Receiver</th>
            <th>PIC Receiver</th>
            <th>Condition Receive</th>
            <th>Requestor</th>
            <th>Approver 1</th>
            <th>Validator</th>
            <th>Approver 2</th>
            <th>Est Transfer Date</th>
            <th>Request Date</th>
            <th>Cancel Request Date</th>
            <th>Approver 1 Date</th>
            <th>UnApprove Approver 1 Date</th>
            <th>Confirm Validator Date</th>
            <th>Reject Validator Date</th>
            <th>Approver 2 Date</th>
            <th>UnApprove Approver 2 Date</th>
            <th>Confirm Sender Date</th>
            <th>Reject Sender Date</th>
            <th>Accepted Receiver Date</th>
            <th>Reject Receiver Date</th>
            <th>Note Request</th>
            <th>Reason Rejected</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->status_mutation_desc }}</td>
            <td>{{ $item->number }}</td>
            <td>{{ $item->number_sub }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->spec_user }}</td>
            <td>{{ $item->remark }}</td>
            <td align="right">{{ $item->qty_mutation }}</td>
            <td>{{ $item->uom }}</td>
            <td>{{ $item->from_plant_initital . ' ' . $item->from_plant_name }}</td>
            <td>{{ $item->from_cost_center . ' - ' . $item->from_cost_center_code }}</td>
            <td>{{ $item->pic_sender }}</td>
            <td>{{ $item->condition_send }}</td>
            <td>{{ $item->to_plant_initital . ' ' . $item->to_plant_name }}</td>
            <td>{{ $item->to_cost_center . ' - ' . $item->to_cost_center_code }}</td>
            <td>{{ $item->pic_receiver }}</td>
            <td>{{ $item->condition_receive }}</td>
            <td>{{ App\Models\User::getNameById($item->user_id) . ' (' . $item->requestor . ')' }}</td>
            <td>{{ App\Models\User::getNameById($item->level_request_first_id) . ' (' . $item->level_request_first . ')' }}</td>
            <td>{{ App\Models\Financeacc\AssetValidator::getNameById($item->asset_validator_id) }}</td>
            <td>{{ App\Models\User::getNameById($item->level_request_second_id) . ' (' . $item->level_request_second . ')' }}</td>
            <td>{{ ($item->date_send_est != '' && $item->date_send_est != null) ? App\Library\Helper::DateConvertFormat($item->date_send_est, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_request != '' && $item->date_request != null) ? App\Library\Helper::DateConvertFormat($item->date_request, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_cancel_request != '' && $item->date_cancel_request != null) ? App\Library\Helper::DateConvertFormat($item->date_cancel_request, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_approve_first != '' && $item->date_approve_first != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_first, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_unapprove_first != '' && $item->date_unapprove_first != null) ? App\Library\Helper::DateConvertFormat($item->date_unapprove_first, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_confirmation_validator != '' && $item->date_confirmation_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_reject_validator != '' && $item->date_reject_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_approve_second != '' && $item->date_approve_second != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_second, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_unapprove_second != '' && $item->date_unapprove_second != null) ? App\Library\Helper::DateConvertFormat($item->date_unapprove_second, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_confirmation_sender != '' && $item->date_confirmation_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_reject_sender != '' && $item->date_reject_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_confirmation_sender != '' && $item->date_confirmation_sender != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_reject_receiver != '' && $item->date_reject_receiver != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_receiver, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ $item->note_request }}</td>
            <td>{{ $item->reason_rejected }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
