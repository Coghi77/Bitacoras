
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
        Schema::create(('evento'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bitacora')->constrained('bitacora')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_seccion')->constrained('seccione');
            $table->foreignId('id_subarea')->constrained('subarea');
            $table->foreignId('id_horario')->constrained('horarios');
            $table->foreignId('id_horario_leccion')->constrained('horario_leccion');
            $table->timestamp('fecha')->useCurrent();
            $table->time('hora_envio');
            $table->string('observacion',255);
            $table->string('prioridad',255);
            $table->boolean('confirmacion');
            $table->tinyInteger('condicion')->default(1);
            $table->enum('estado', ['en_espera', 'en_proceso', 'completado'])->default('en_espera');
            $table->boolean('enviar_soporte');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
