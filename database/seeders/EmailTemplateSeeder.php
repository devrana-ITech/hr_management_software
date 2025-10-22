<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('email_templates')->insert([
            [
                "name"                => "New Message",
                "slug"                => "NEW_MESSAGE",
                "subject"             => "You Have a New Message",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'><p>Hi {{recipientName}},</p> <p>I hope this email finds you well.</p> <p>This is to inform you that you have received a new message. <p>Please login to your account to view the full message.</p></div>",
                "sms_body"            => "You have received a new message. Please login to your account to see the message.",
                "notification_body"   => "<p>Hi {{recipientName}},</p><p>You have received a new message.</p><p><a class='btn btn-primary btn-xs mt-2' href='{{messageLink}}'>View Message</a></p>",
                "shortcode"           => "{{recipientName}} {{loginUrl}} {{messageLink}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
			[
                "name"                => "New Leave Application Submitted",
                "slug"                => "NEW_LEAVE_APPLICATION",
                "subject"             => "New Leave Application Submitted",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>New Leave Application Submitted</h2> <p>I am writing to let you know that a new leave application has been submitted by an employee.</p> <p><a href='{{applicationLink}}'>View Application</a></p></div>",
                "sms_body"            => "New Leave Application Submitted",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>New Leave Application Submitted</h5> <p>I am writing to let you know that a new leave application has been submitted by an employee.</p> <p><a class='btn btn-primary btn-xs mt-2' href='{{applicationLink}}'>View Application</a></p>",
                "shortcode"           => "{{applicationLink}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
			[
                "name"                => "Leave Application Approved",
                "slug"                => "LEAVE_APPLICATION_APPROVED",
                "subject"             => "Leave Application Approved",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Leave Application Approved</h2> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your leave application has been approved.</p></div>",
                "sms_body"            => "Your Leave Application has been approved",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>Leave Application Approved</h5> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your leave application has been approved.</p>",
                "shortcode"           => "{{employeeName}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
            [
                "name"                => "Leave Application Rejected",
                "slug"                => "LEAVE_APPLICATION_REJECTED",
                "subject"             => "Leave Application Rejected",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Leave Application Rejected</h2> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your leave application has been rejected. You can contact with the authority for more details.</p></div>",
                "sms_body"            => "Your Leave Application has been rejected",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>Leave Application Rejected</h5> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your leave application has been rejected. You can contact with the authority for more details.</p>",
                "shortcode"           => "{{employeeName}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
            [
                "name"                => "New Loan Application Submitted",
                "slug"                => "NEW_LOAN_APPLICATION",
                "subject"             => "New Loan Application Submitted",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>New Loan Application Submitted</h2> <p>I am writing to let you know that a new loan application has been submitted by an employee.</p> <p><a href='{{applicationLink}}'>View Application</a></p></div>",
                "sms_body"            => "New Loan Application Submitted",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>New Loan Application Submitted</h5> <p>I am writing to let you know that a new loan application has been submitted by an employee.</p> <p><a href='{{applicationLink}}' class='btn btn-primary btn-xs mt-2'>View Application</a></p>",
                "shortcode"           => "{{applicationLink}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
            [
                "name"                => "Loan Application Approved",
                "slug"                => "LOAN_APPLICATION_APPROVED",
                "subject"             => "Loan Application Approved",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Loan Application Approved</h2> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your Loan application has been approved.</p></div>",
                "sms_body"            => "Your Loan Application has been approved",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>Loan Application Approved</h5> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your Loan application has been approved.</p>",
                "shortcode"           => "{{employeeName}} {{loanAmount}} {{monthlyInstallment}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
            [
                "name"                => "Loan Application Rejected",
                "slug"                => "LEAVE_APPLICATION_REJECTED",
                "subject"             => "Loan Application Rejected",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Loan Application Rejected</h2> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your Loan application has been rejected. You can contact with the authority for more details.</p></div>",
                "sms_body"            => "Your Leave Application has been rejected",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>Loan Application Rejected</h5> <p>Dear {{employeeName}},</p> <p>We hope this email finds you well. We wanted to inform you that your Loan application has been rejected. You can contact with the authority for more details.</p>",
                "shortcode"           => "{{employeeName}} {{loanAmount}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
            [
                "name"                => "Payslip Notification",
                "slug"                => "PAYSLIP_NOTIFICATION",
                "subject"             => "Access Your Payslip for {{monthYear}}",
                "email_body"          => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Your payslip for the period of {{monthYear}} is now available.</h2></div>",
                "sms_body"            => "Your payslip for the period of {{monthYear}} is now available.",
                "notification_body"   => "<h5 style='color: #333333; margin-bottom: 10px;'>Your payslip for the period of {{monthYear}} is now available.</h5>",
                "shortcode"           => "{{monthYear}}",
                "email_status"        => 0,
                "sms_status"          => 0,
                "notification_status" => 0,
                "template_mode"       => 0,
            ],
        ]);
    }
}
