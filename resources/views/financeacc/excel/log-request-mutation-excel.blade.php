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
            <th>Current Step</th>
            <th>Plant Sender</th>
            <th>Cost Center Sender</th>
            <th>Plant Receiver</th>
            <th>Cost Center Receiver</th>
            <th>Asset Number</th>
            <th>Sub Number</th>
            <th>Description</th>
            <th>Spec / User</th>
            <th>Remark</th>
            <th>Qty</th>
            <th>Uom</th>
            <th>Validator</th>
            <th>Request Date</th>
            <th>Cancel Date</th>
            <th>Approved Date</th>
            <th>UnApprove Date</th>
            <th>Confirmation Date</th>
            <th>Rejected Date</th>
            <th>Send Date</th>
            <th>Note Rejected</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['items'] as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->step_request_desc }}</td>
            <td>{{ $item->from_plant_initital . ' ' . $item->from_plant_name }}</td>
            <td>{{ $item->from_cost_center }}</td>
            <td>{{ $item->to_plant_initital . ' ' . $item->to_plant_name }}</td>
            <td>{{ $item->to_cost_center }}</td>
            <td>{{ $item->number }}</td>
            <td>{{ $item->number_sub }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->spec_user }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->qty_mutation }}</td>
            <td>{{ $item->uom }}</td>
            <td>{{ $item->validator }}</td>
            <td>{{ ($item->date_submit != '' && $item->date_submit != null) ? App\Library\Helper::DateConvertFormat($item->date_submit, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_cancel != '' && $item->date_cancel != null) ? App\Library\Helper::DateConvertFormat($item->date_cancel, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_approve_hod != '' && $item->date_approve_hod != null) ? App\Library\Helper::DateConvertFormat($item->date_approve_hod, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_unapprove_hod != '' && $item->date_unapprove_hod != null) ? App\Library\Helper::DateConvertFormat($item->date_unapprove_hod, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_confirmation_validator != '' && $item->date_confirmation_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_reject_validator != '' && $item->date_reject_validator != null) ? App\Library\Helper::DateConvertFormat($item->date_reject_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ ($item->date_send != '' && $item->date_send != null) ? App\Library\Helper::DateConvertFormat($item->date_send, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-' }}</td>
            <td>{{ $item->note_rejected }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
