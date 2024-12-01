<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('provider');
            $table->string('provider_account_id');
            $table->text('refresh_token')->nullable();
            $table->text('access_token')->nullable();
            $table->string('token_type')->nullable();
            $table->string('scope')->nullable();
            $table->text('id_token')->nullable();
            $table->string('session_state')->nullable();
            $table->string('oauth_token_secret')->nullable();
            $table->string('oauth_token')->nullable();
            $table->integer('refresh_token_expires_in')->nullable();
            $table->integer('expires_at')->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['provider', 'provider_account_id'], 'provider_provider_account_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
