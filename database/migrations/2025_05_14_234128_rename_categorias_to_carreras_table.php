    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         * Renombra la tabla 'categorias' a 'carreras'.
         */
        public function up(): void
        {
            Schema::rename('categorias', 'carreras');
        }

        /**
         * Reverse the migrations.
         * Renombra la tabla 'carreras' de nuevo a 'categorias'.
         */
        public function down(): void
        {
            Schema::rename('carreras', 'categorias');
        }
    };
    