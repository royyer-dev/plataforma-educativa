    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         * Renombra 'categoria_id' a 'carrera_id' y actualiza la clave foránea.
         */
        public function up(): void
        {
            Schema::table('cursos', function (Blueprint $table) {
                // 1. Eliminar la clave foránea antigua.
                // El nombre por defecto para una FK creada con constrained() en 'categoria_id'
                // que referencia 'categorias' es 'cursos_categoria_id_foreign'.
                $table->dropForeign(['categoria_id']); // Laravel debería poder resolver esto.
                                                      // Si falla, usa: $table->dropForeign('cursos_categoria_id_foreign');

                // 2. Renombrar la columna
                $table->renameColumn('categoria_id', 'carrera_id');

                // 3. Añadir la nueva clave foránea apuntando a la tabla 'carreras'
                $table->foreign('carrera_id')
                      ->references('id')
                      ->on('carreras') // Apunta a la tabla 'carreras' (que fue renombrada)
                      ->onDelete('set null')
                      ->onUpdate('cascade');
            });
        }

        /**
         * Reverse the migrations.
         * Renombra 'carrera_id' de nuevo a 'categoria_id' y revierte la clave foránea.
         */
        public function down(): void
        {
            Schema::table('cursos', function (Blueprint $table) {
                // 1. Eliminar la nueva clave foránea
                $table->dropForeign(['carrera_id']); // Laravel debería adivinar 'cursos_carrera_id_foreign'
                                                    // Si falla, usa: $table->dropForeign('cursos_carrera_id_foreign');

                // 2. Renombrar la columna de vuelta
                $table->renameColumn('carrera_id', 'categoria_id');

                // 3. Recrear la clave foránea original apuntando a 'categorias'
                 $table->foreign('categoria_id')
                      ->references('id')
                      ->on('categorias') // Apunta de nuevo a la tabla 'categorias'
                      ->onDelete('set null')
                      ->onUpdate('cascade');
            });
        }
    };
    