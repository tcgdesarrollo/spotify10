<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queue_emails', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Tipo de correo para identificar');
            $table->json('to')->comment('Lista de destinatarios');
            $table->string('language')->default('es')->comment('Idioma para enviar el email');
            $table->text('sent_result')->nullable()->comment('Resultado de envío');
            $table->json('extra')->nullable()->comment('Datos extras para conformar el email');
            $table->foreignId('user_id')->nullable()->comment('Usuario al que va dirigido')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_emails');
    }
};
