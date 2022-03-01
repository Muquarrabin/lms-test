<?php

namespace App\Jobs;

use App\Mail\ForgetPasswordEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $userName;
    public $userToken;
    public $userEmail;
    public $generatedUrl;
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
        $this->generatedUrl = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::to($this->userEmail)->send(new ForgetPasswordEmail( $this->userId, $this->userName, $this->userEmail, $this->userToken, $this->generatedUrl));

    }
}
