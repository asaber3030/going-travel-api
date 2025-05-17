<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Mail::to('abdulrahmansaber120@gmail.com')->send(new ContactMail($validated));

        return sendResponse(__('messages.email_sent_successfully'), 200);
    }
}
