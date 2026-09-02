<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $templates = [
            ['name' => 'Bank Withdrawal User Requested', 'slug' => 'bank-withdraw-user-requested', 'subject' => 'Bank withdrawal request submitted - [[site_name]]', 'greeting' => 'Hello [[user_name]]', 'message' => 'Your bank withdrawal request has been submitted and is pending review.<br><br><strong>Withdrawal details</strong><br>Amount: [[amount]] [[currency]]<br>Reference: [[trx]]<br>Status: Pending<br><br><strong>Bank details</strong><br>[[bank_details]]', 'regards' => 'true'],
            ['name' => 'Bank Withdrawal Admin Approved', 'slug' => 'bank-withdraw-admin-approved', 'subject' => 'Bank withdrawal approved - [[site_name]]', 'greeting' => 'Hello [[user_name]]', 'message' => 'Your bank withdrawal has been approved.<br><br><strong>Withdrawal details</strong><br>Amount: [[amount]] [[currency]]<br>Reference: [[trx]]<br>Status: Approved<br><br><strong>Bank details</strong><br>[[bank_details]]<br><br>Admin note: [[admin_details]]', 'regards' => 'true'],
        ];
        foreach ($templates as $template) {
            DB::table('email_templates')->updateOrInsert(['slug' => $template['slug']], $template + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        DB::table('email_templates')->whereIn('slug', ['bank-withdraw-user-requested', 'bank-withdraw-admin-approved'])->delete();
    }
};
