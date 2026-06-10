<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_chat_rooms', function (Blueprint $table) {
            if (! Schema::hasColumn('group_chat_rooms', 'visibility')) {
                $table->string('visibility', 20)->default('public')->after('description');
            }

            if (! Schema::hasColumn('group_chat_rooms', 'invite_token')) {
                $table->string('invite_token', 80)->nullable()->unique()->after('visibility');
            }
        });

        Schema::table('group_chat_members', function (Blueprint $table) {
            if (! Schema::hasColumn('group_chat_members', 'anonymous_name')) {
                $table->string('anonymous_name')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('group_chat_members', 'membership_status')) {
                $table->string('membership_status', 20)->default('active')->after('anonymous_name');
            }

            if (! Schema::hasColumn('group_chat_members', 'consented_at')) {
                $table->timestamp('consented_at')->nullable()->after('joined_at');
            }

            if (! Schema::hasColumn('group_chat_members', 'consent_version')) {
                $table->string('consent_version', 40)->nullable()->after('consented_at');
            }

            if (! Schema::hasColumn('group_chat_members', 'joined_via')) {
                $table->string('joined_via', 30)->nullable()->after('consent_version');
            }

            if (! Schema::hasColumn('group_chat_members', 'invited_by')) {
                $table->foreignId('invited_by')->nullable()->after('joined_via')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('group_chat_members', 'removed_at')) {
                $table->timestamp('removed_at')->nullable()->after('invited_by');
            }

            if (! Schema::hasColumn('group_chat_members', 'removed_reason')) {
                $table->string('removed_reason', 100)->nullable()->after('removed_at');
            }
        });

        Schema::table('notifikasi', function (Blueprint $table) {
            if (! Schema::hasColumn('notifikasi', 'cta_target')) {
                $table->text('cta_target')->nullable()->after('pesan');
            }

            if (! Schema::hasColumn('notifikasi', 'cta_label')) {
                $table->string('cta_label', 80)->nullable()->after('cta_target');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_chat_members', function (Blueprint $table) {
            if (Schema::hasColumn('group_chat_members', 'invited_by')) {
                $table->dropConstrainedForeignId('invited_by');
            }

            $columns = [
                'anonymous_name',
                'membership_status',
                'consented_at', 
                'consent_version',
                'joined_via',
                'removed_at',
                'removed_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('group_chat_members', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('group_chat_rooms', function (Blueprint $table) {
            foreach (['visibility', 'invite_token'] as $column) {
                if (Schema::hasColumn('group_chat_rooms', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('notifikasi', function (Blueprint $table) {
            foreach (['cta_target', 'cta_label'] as $column) {
                if (Schema::hasColumn('notifikasi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
