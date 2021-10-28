<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $judul;
    public $info_pengguna;
    public $stat;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($judul,$info_pengguna,$stat)
    {
        $this->judul = $judul;
        $this->info_pengguna = $info_pengguna;
        $this->stat = $stat;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->judul) ->view('Mail/customer_mail'); //customer_mail is name template
    }
}
