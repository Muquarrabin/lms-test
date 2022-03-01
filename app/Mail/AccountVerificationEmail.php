<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $userName;
    public $userToken;
    public $userEmail;
    public $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $userName, $userEmail, $userToken, $url)
    {
        //
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userToken = $userToken;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view("mail.accountMail")->subject("Account Verification Mail")
            ->with([
                'userId' => $this->userId,
                'userToken' => $this->userToken,
                'url' => $this->url
            ]);
        //->setBody("A test Email Body");
    }
}
