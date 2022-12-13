<?php

namespace App\Mail\Financeacc\Assets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\User;
use App\Models\Financeacc\AssetValidator;

class NotificationMutation extends Mailable
{
    use Queueable, SerializesModels;

    public $id;

    public $subjectMail;
    public $toMail = [];
    public $ccMail = [];
    public $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;

        $this->init();
    }

    public function init()
    {
        /*
            status mutation
            1 = request
            2 = cancel request
            3 = approve approver 1
            4 = unapprove approver 1
            5 = confirmation validator
            6 = reject by validator
            7 = approve approver 2
            8 = unapprove approver 2
            9 = approve approver 3
            10 = unapprove approver 3
            11 = confirmation send sender
            12 = reject sender
            13 = accept receiver
            14 = reject receiver
        */

        $asset = DB::table('asset_mutations')
                    ->where('id', $this->id)
                    ->first();

        $this->subjectMail = '';
        $this->ccMail = [];
        $this->toMail = [];
        $this->data = [];

        $plant_send = Plant::getDataPlantById($asset->from_plant_id);
        $plant_receiver = Plant::getDataPlantById($asset->to_plant_id);
        $am_plant_send = Plant::getDataAMPlantById($asset->from_plant_id);
        $am_plant_receiver = Plant::getDataAMPlantById($asset->to_plant_id);

        $approve1Email = User::getEmailById($asset->level_request_first_id);
        $approve1Name = User::getNameById($asset->level_request_first_id);
        $approve2Email = User::getEmailById($asset->level_request_second_id);
        $approve2Name = User::getNameById($asset->level_request_second_id);
        $approve3Email = User::getEmailById($asset->level_request_third_id);
        $approve3Name = User::getNameById($asset->level_request_third_id);
        $requestor = User::getNameById($asset->user_id) . ' (' . $asset->requestor . ')';
        if( $asset->requestor == 'admin department' ){
            $requestor .= ' (' . User::getDepartmentById($asset->user_id) . ')';
        }

        $senderCostCenterEmail = User::getEmailById($asset->sender_cost_center_id);
        $senderCostCenterName = User::getNameById($asset->sender_cost_center_id);
        $receiverCostCenterEmail = User::getEmailById($asset->receiver_cost_center_id);
        $receiverCostCenterName = User::getNameById($asset->receiver_cost_center_id);

        $this->data = [
            'asset' => $asset,
            'plant_send' => $plant_send,
            'plant_receiver' => $plant_receiver,
            'am_plant_send' => $am_plant_send,
            'am_plant_receiver' => $am_plant_receiver,
            'approve1_name' => $approve1Name,
            'approve2_name' => $approve2Name,
            'approve3_name' => $approve3Name,
            'sender_costcenter_name' => $senderCostCenterName,
            'receiver_costcenter_name' => $receiverCostCenterName,
            'requestor' => $requestor,
            'validator' => AssetValidator::getNameById($asset->asset_validator_id),
            'validator_assign' => AssetValidator::getNameById($asset->assign_asset_validator_id),
        ];

        switch ($asset->status_mutation) {
            // approved
            case '1':
                $this->subjectMail = Lang::get("Request Asset Transfer Web");
                // send to RM / HOD
                $this->toMail = $approve1Email;
                break;
            case '2':
                $this->subjectMail = Lang::get("Cancel Request Asset Transfer Web");
                // send to RM / HOD
                $this->toMail = $approve1Email;
                break;
            case '3':
                $this->subjectMail = Lang::get("Confirmation Validator Asset Transfer Web");
                // send to validator
                $this->toMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);
                break;
            case '4':
                $this->subjectMail = Lang::get("UnApprove Request Asset Transfer Web");
                $this->toMail = User::getEmailById($asset->user_id);
                break;
            case '5':
                $this->subjectMail = Lang::get("Approve Asset Transfer Web");
                // send to approver2
                $this->toMail = $approve2Email;
                break;
            case '6':
                $this->subjectMail = Lang::get("Rejected Request Asset Transfer Web");
                // send to requestor
                $this->toMail = User::getEmailById($asset->user_id);
                $this->ccMail[] = $approve1Email;
                break;
            case '7':
                if( $asset->level_request_third_id != '0' ){
                    // send to approver 3
                    $this->subjectMail = Lang::get("Approve Asset Transfer Web");
                    $this->toMail = $approve3Email;
                } else {
                    // plant sender
                    $this->subjectMail = Lang::get("Confirmation Send Asset Transfer Web");
                    if( $asset->sender_cost_center_id != '0' ){
                        // to admin cost center
                        $this->toMail = $senderCostCenterEmail;
                    } else {
                        // to plant sender
                        $this->toMail = $plant_send->email;
                    }
                }
                break;
            case '8':
                $this->subjectMail = Lang::get("UnApprove Asset Transfer Web");
                // send to requestor
                $this->toMail = User::getEmailById($asset->user_id);
                $this->ccMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);
                array_push($this->ccMail, $approve1Email);
                break;
            case '9':
                $this->subjectMail = Lang::get("Confirmation Send Asset Transfer Web");
                // plant sender
                $this->toMail = $plant_send->email;
                break;
            case '10':
                $this->subjectMail = Lang::get("UnApprove Asset Transfer Web");
                // send to requestor
                $this->toMail = User::getEmailById($asset->user_id);
                $this->ccMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);
                array_push($this->ccMail, $approve1Email, $approve2Email);
                break;
            case '11':
                $this->subjectMail = Lang::get("Confirmation Accept Asset Transfer Web");
                // plant receiver
                if( $asset->receiver_cost_center_id != '0' ){
                    // to cost center receipt admin
                    $this->toMail = $receiverCostCenterEmail;
                }else {
                    // to plant receiver
                    $this->toMail = $plant_receiver->email;
                }

                break;
            case '12':
                $this->subjectMail = Lang::get("Reject Sender Asset Transfer Web");
                // send to requestor
                $this->toMail = User::getEmailById($asset->user_id);
                $this->ccMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);
                if( $asset->level_request_third_id != 0 ){
                    array_push($this->ccMail, $approve1Email, $approve2Email, $approve3Email);
                } else {
                    array_push($this->ccMail, $approve1Email, $approve2Email);
                }
                break;
            case '13':
                $this->subjectMail = Lang::get("Accepted Asset Transfer Web");
                $this->toMail[] = User::getEmailById($asset->user_id);
                $this->ccMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);

                if( $asset->sender_cost_center_id != '0' ){
                    // to admin cost center
                    $plantSendEmail = $senderCostCenterEmail;
                } else {
                    // to plant sender
                    $plantSendEmail = $plant_send->email;
                }

                if( $asset->receiver_cost_center_id != '0' ){
                    // to cost center receipt admin
                    $plantReceiveEmail = $receiverCostCenterEmail;
                }else {
                    // to plant receiver
                    $plantReceiveEmail = $plant_receiver->email;
                }

                if( $asset->level_request_third_id != 0 ){
                    array_push($this->ccMail, $approve1Email, $approve2Email, $approve3Email, $plantSendEmail, $plantReceiveEmail, 'asset1.qsr@richeesefactory.com', 'asset2.qsr@richeesefactory.com', 'asset3.qsr@richeesefactory.com');
                } else {
                    array_push($this->ccMail, $approve1Email, $approve2Email, $plantSendEmail, $plantReceiveEmail, 'asset1.qsr@richeesefactory.com', 'asset2.qsr@richeesefactory.com', 'asset3.qsr@richeesefactory.com');
                }

                break;
            case '14':
                $this->subjectMail = Lang::get("Reject Accept Receiver Asset Transfer Web");
                // send to requestor
                $this->toMail = User::getEmailById($asset->user_id);
                $this->ccMail = AssetValidator::getListEmailPicValidator($asset->asset_validator_id, $asset->from_plant_id);

                if( $asset->sender_cost_center_id != '0' ){
                    // to admin cost center
                    $plantSendEmail = $senderCostCenterEmail;
                } else {
                    // to plant sender
                    $plantSendEmail = $plant_send->email;
                }

                if( $asset->level_request_third_id != 0 ){
                    array_push($this->ccMail, $approve1Email, $approve2Email, $approve3Email, $plantSendEmail);
                } else {
                    array_push($this->ccMail, $approve1Email, $approve2Email, $plantSendEmail);
                }

                break;
        }
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subjectMail,
            to: 'yudhapermana.dev@gmail.com',
            // to: $this->toMail
            // cc: $this->ccMail
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.financeacc.assets.notification-mutation',
            with: $this->data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
