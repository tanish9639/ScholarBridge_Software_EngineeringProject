<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('department')->constrained('departments')->nullOnDelete();
            $table->string('first_name')->nullable()->after('birthday');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('blood_group')->nullable()->after('last_name');
            $table->string('matriculation')->nullable()->after('blood_group');
            $table->string('guardian_phone')->nullable()->after('matriculation');
            $table->string('mother_name')->nullable()->after('guardian_phone');
            $table->string('father_name')->nullable()->after('mother_name');
            $table->text('address')->nullable()->after('father_name');
            $table->string('pin_code')->nullable()->after('address');
            $table->string('state')->nullable()->after('pin_code');
            $table->string('city')->nullable()->after('state');
            $table->string('secondary_education')->nullable()->after('city');
            $table->string('graduation')->nullable()->after('secondary_education');
            $table->string('program_level')->nullable()->after('graduation');
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('department')->constrained('departments')->nullOnDelete();
            $table->string('first_name')->nullable()->after('phone');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('blood_group')->nullable()->after('last_name');
            $table->string('matriculation')->nullable()->after('blood_group');
            $table->string('guardian_phone')->nullable()->after('matriculation');
            $table->string('mother_name')->nullable()->after('guardian_phone');
            $table->string('father_name')->nullable()->after('mother_name');
            $table->text('address')->nullable()->after('father_name');
            $table->string('pin_code')->nullable()->after('address');
            $table->string('state')->nullable()->after('pin_code');
            $table->string('city')->nullable()->after('state');
            $table->string('secondary_education')->nullable()->after('city');
            $table->string('graduation')->nullable()->after('secondary_education');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'blood_group',
                'matriculation',
                'guardian_phone',
                'mother_name',
                'father_name',
                'address',
                'pin_code',
                'state',
                'city',
                'secondary_education',
                'graduation',
                'program_level',
            ]);
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'blood_group',
                'matriculation',
                'guardian_phone',
                'mother_name',
                'father_name',
                'address',
                'pin_code',
                'state',
                'city',
                'secondary_education',
                'graduation',
            ]);
        });
    }
};
