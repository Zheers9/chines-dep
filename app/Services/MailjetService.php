<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailjetService
{
    /**
     * Send an email using Laravel's default Mailer (configured via .env).
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlPart)
    {
        try {
            Log::info('MailService: Attempting to send email', [
                'to' => $toEmail,
                'subject' => $subject,
                'mailer' => config('mail.default')
            ]);

            Mail::html($htmlPart, function ($message) use ($toEmail, $toName, $subject) {
                $message->to($toEmail, $toName)
                        ->subject($subject);
            });

            Log::info('MailService: Success');

            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('MailService: Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get the base HTML template for consistency.
     */
    private function getBaseTemplate($title, $content, $ctaLabel = null, $ctaLink = null)
    {
        $primaryColor = '#ef4444'; // Red branding

        $ctaHtml = $ctaLabel ? "
            <div style='margin: 40px 0; text-align: center;'>
                <a href='{$ctaLink}' style='background-color: {$primaryColor}; color: white; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);'>
                    {$ctaLabel}
                </a>
            </div>" : "";

        return "
        <div style='background-color: #f8fafc; padding: 40px 20px; font-family: \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);'>
                <div style='background: linear-gradient(135deg, #f87171, #ef4444); padding: 40px 20px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px; letter-spacing: 1px;'>{$title}</h1>
                </div>
                <div style='padding: 40px; color: #1e293b; line-height: 1.6;'>
                    {$content}
                    {$ctaHtml}
                    <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 40px 0;'>
                    <p style='font-size: 13px; color: #64748b; text-align: center;'>
                        This is an automated message from the <strong>Chinese Department Admissions System</strong>.<br>
                        Please do not reply directly to this email.
                    </p>
                </div>
                <div style='background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; " . date('Y') . " Salahaddin University - Erbil. All Rights Reserved.
                </div>
            </div>
        </div>";
    }

    /**
     * Send Registration Accepted Email.
     */
    public function sendRegistrationAccepted($user, $registration)
    {
        $title = "Registration Approved!";
        $subject = "Congratulations! Your HSK Registration is Approved";
        
        $content = "
            <p style='font-size: 18px; margin-bottom: 24px;'>Hello <strong>{$user->full_name}</strong>,</p>
            <p><strong>Congratulations!</strong> We have reviewed your application for the <strong>{$registration->examSubType->name}</strong> and it has been officially <strong>Approved</strong>.</p>
            
            <div style='background-color: #fff7ed; border-left: 4px solid #f97316; padding: 20px; margin: 30px 0;'>
                <p style='margin: 0; font-weight: bold; color: #9a3412;'>Next Step: Payment Required</p>
                <p style='margin: 10px 0 0; font-size: 14px;'>
                    To finalize your seat in the exam, please visit the department to <strong>pay your registration fee</strong>. 
                    Once paid, your registration will be fully confirmed.
                </p>
            </div>

            <div style='background-color: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 20px; margin: 30px 0;'>
                <p style='margin: 0; font-weight: bold; color: #0369a1;'>Start Preparing Today:</p>
                <p style='margin: 10px 0 0; font-size: 14px;'>
                    While you wait, you can already start practicing for your exam online through our study roadmap.
                </p>
            </div>

            <p>We look forward to seeing you soon!</p>";

        $html = $this->getBaseTemplate($title, $content, "Start Practicing Online", "http://localhost:5173/hsk");
        
        return $this->sendEmail($user->email, $user->full_name, $subject, $html);
    }

    /**
     * Send Payment Confirmation Email.
     */
    public function sendPaymentConfirmation($user, $payment)
    {
        $title = "Payment Received";
        $subject = "Receipt: Payment Confirmed for {$payment->examSubType->name}";
        
        $content = "
            <p style='font-size: 18px; margin-bottom: 24px;'>Hello <strong>{$user->full_name}</strong>,</p>
            <p>This is a formal confirmation that we have received your payment for the <strong>{$payment->examSubType->name}</strong> registration.</p>
            <div style='background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 20px; margin: 30px 0;'>
                <p style='margin: 0; font-weight: bold; color: #15803d;'>Transaction Details:</p>
                <p style='margin: 10px 0 0; font-size: 14px;'>
                    <strong>Voucher No:</strong> <code>{$payment->voucher_num}</code><br>
                    <strong>Amount Paid:</strong> {$payment->pay} IQD<br>
                    <strong>Date:</strong> " . date('M d, Y') . "
                </p>
            </div>
            <p>Your payment has been successfully logged into our financial records. Please keep this email as your official receipt.</p>";

        $html = $this->getBaseTemplate($title, $content, "View My Account", "http://localhost:5173/profile");
        
        return $this->sendEmail($user->email, $user->full_name, $subject, $html);
    }
}
