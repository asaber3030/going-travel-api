<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
  use Queueable, SerializesModels;

  public $data;

  public function __construct(array $data)
  {
    $this->data = $data;
  }

  public function build()
  {
    return $this->subject($this->data['subject'])
      ->from($this->data['email'], $this->data['name'])
      ->view('emails.contact');
  }
}
