<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
   Schema::create('invoices', function (Blueprint $table) {

    $table->id();

    $table->string('vendor')->nullable();

    $table->date('invoice_date')->nullable();

    $table->string('reference')->nullable();

    $table->decimal('amount',12,2)->nullable();

    $table->string('currency',10)->nullable();

    $table->string('text')->nullable();

    $table->string('file_path')->nullable();

    $table->string('status')->default('uploaded');

    $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
