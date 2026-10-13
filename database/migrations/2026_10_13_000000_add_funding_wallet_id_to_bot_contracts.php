<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('bot_contracts', 'funding_wallet_id')) {
            Schema::table('bot_contracts', function (Blueprint $table) {
                $table->unsignedBigInteger('funding_wallet_id')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('bot_contracts', function (Blueprint $table) {
            $table->dropColumn('funding_wallet_id');
        });
    }
};
