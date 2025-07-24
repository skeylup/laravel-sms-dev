<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to')->index(); // Numéro de téléphone destinataire
            $table->string('from')->nullable(); // Numéro expéditeur
            $table->text('message'); // Contenu du SMS
            $table->json('metadata')->nullable(); // Données supplémentaires (notification class, etc.)
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable(); // Quand le SMS a été "envoyé"
            $table->boolean('is_read')->default(false)->index(); // Pour marquer comme lu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
