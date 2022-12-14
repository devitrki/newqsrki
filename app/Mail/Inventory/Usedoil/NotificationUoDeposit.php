<?php

namespace App\Mail\Inventory\Usedoil;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Mail\Mailables\Attachment;

use App\Library\Helper;

use App\Models\Configuration;
use App\Models\User;

class NotificationUoDeposit extends Mailable
{
    use Queueable, SerializesModels;

    public $id;
    public $type;
    public $uoDeposit;

    public $subjectMail;
    public $toMail = [];
    public $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;

        $this->init();
    }

    public function init()
    {
        $uoDeposit = DB::table('uo_deposits')
                    ->join('uo_vendors', 'uo_vendors.id', 'uo_deposits.uo_vendor_id')
                    ->join('companies', 'companies.id', 'uo_deposits.company_id')
                    ->select(['uo_deposits.id', 'uo_deposits.uo_vendor_id', 'uo_deposits.document_number', 'uo_deposits.deposit_date',
                    'uo_deposits.richeese_bank', 'uo_deposits.type_deposit', 'uo_deposits.transfer_bank_account',
                    'uo_deposits.transfer_bank_account_name', 'uo_deposits.deposit_nominal', 'uo_deposits.submit', 'uo_deposits.confirmation_fa',
                    'uo_deposits.transfer_bank', 'uo_deposits.created_id','uo_vendors.name as vendor_name', 'uo_deposits.reject_description',
                    'uo_deposits.created_by', 'uo_deposits.image', 'uo_vendors.company_id', 'companies.name as company_name'])
                    ->where('uo_deposits.id', $this->id)
                    ->first();

        $this->uoDeposit = $uoDeposit;

        $emailFa = Configuration::getValueCompByKeyFor($uoDeposit->company_id, 'inventory', 'uo_email_fa');
        $emailFa = explode(',', $emailFa);

        $uoDeposit->deposit_date_desc = date("d-m-Y", strtotime($uoDeposit->deposit_date));
        $uoDeposit->deposit_nominal_desc = Helper::convertNumberToInd($uoDeposit->deposit_nominal, '', 0);

        if( $uoDeposit->type_deposit != '1'){
            $uoDeposit->type_deposit_desc = Lang::get('Bank Transfer');
        } else {
            $uoDeposit->type_deposit_desc = Lang::get('Deposit Cash');
        }

        $this->subjectMail = '';
        $this->toMail = [];

        $this->data = [
            'uo_deposit' => $uoDeposit,
            'type' => $this->type,
        ];

        switch ($this->type) {
            case 'submit':
                $this->subjectMail = Lang::get("Deposit Vendor Used Oil") . " " . $uoDeposit->company_name;
                $this->toMail = $emailFa;
                break;
            case 'confirm':
                $this->subjectMail = Lang::get("Confirmation Deposit Vendor Used Oil")  . " " . $uoDeposit->company_name;
                $this->toMail[] = User::getEmailById($uoDeposit->created_id);
                break;
            case 'reject':
                $this->subjectMail = Lang::get("Reject Deposit Vendor Used Oil") . " " . $uoDeposit->company_name;
                $this->toMail[] = User::getEmailById($uoDeposit->created_id);
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
            // to: 'yudhapermana.dev@gmail.com',
            to: $this->toMail
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
            markdown: 'emails.inventory.usedoil.notification-deposit',
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
        return [
            Attachment::fromStorageDisk('public', 'usedoil/transfer/' . $this->uoDeposit->image)
        ];
    }
}
