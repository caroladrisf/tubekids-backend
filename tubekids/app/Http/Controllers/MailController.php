<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendConfirmationEmail (Request $request, User $user)
    {
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom(getenv('EMAIL_ADDRESS'), "Tubekids");
        $email->setSubject("Tubekids Email Confirmation");
        $email->addTo($user->email, $user->name);
        $email->addContent(
            "text/html", "<link href='https://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet'>
            <div style='background-color: #F9F8FC; padding: 10px 20px;'>
                <h1 style=' font-family: Roboto, sans-serif; color: #444;'>Welcome to Tubekids, $user->name!</h1>
                <p style='font-family: Roboto, sans-serif; color: #444;'>Click the button below to confirm your email address.
                </p>
                <p>
                    <a style='font-family: Roboto, sans-serif;
                    text-decoration: none;
                    color: #fff;
                    background-color: #593196;
                    border-color: #593196;
                    padding: 0.5rem 1rem;
                    font-size: 1.09375rem;
                    line-height: 1.5;
                    border-radius: 0;' 
                    href='http://localhost:8000/api/users/$user->id/confirm' role='button'>Confirm email address</a>
                </p>
            </div>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            return response()->json('', $response->statusCode());
        } catch (Exception $e) {
            return response()->json(['exception' => $e->getMessage()], 500);
        }
    }
}
