<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['boys', 'girls', 'faculty']);
            $table->string('warden_name')->nullable();
            $table->integer('capacity')->default(100);
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('room_number');
            $table->integer('capacity')->default(3);
            $table->integer('occupied')->default(0);
            $table->decimal('monthly_fee', 8, 2)->default(5000);
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('vehicle_number');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->decimal('monthly_fee', 8, 2)->default(3000);
            $table->json('stops')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
    }
};
