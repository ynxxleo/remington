<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('email_templates')->where('slug', 'bank-withdraw-user-requested')->update([
            'message' => 'Your bank withdrawal request has been submitted and is pending review.<br><br><strong>Withdrawal details</strong><br>Amount: [[amount]] [[currency]]<br>Reference: [[trx]]<br>Status: Pending<br><br><strong>Bank account</strong><br>Account Name: [[bank_account_name]]<br>Account Number: [[bank_account_number]]<br><br><strong>Other bank details</strong><br>[[bank_details]]',
            'updated_at' => now(),
        ]);
        DB::table('email_templates')->where('slug', 'bank-withdraw-admin-approved')->update([
            'message' => 'Your bank withdrawal has been approved.<br><br><strong>Withdrawal details</strong><br>Amount: [[amount]] [[currency]]<br>Reference: [[trx]]<br>Status: Approved<br><br><strong>Bank account</strong><br>Account Name: [[bank_account_name]]<br>Account Number: [[bank_account_number]]<br><br><strong>Other bank details</strong><br>[[bank_details]]<br><br>Admin note: [[admin_details]]',
            'updated_at' => now(),
        ]);
    }

    public function down() {}
};
