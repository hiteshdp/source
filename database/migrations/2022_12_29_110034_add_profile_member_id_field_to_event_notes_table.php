<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileMemberIdFieldToEventNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_notes', function (Blueprint $table) {
            $table->integer('profileMemberId')->nullable()->after('userId')->comment('Reference to profile members table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_notes', function (Blueprint $table) {
            $table->dropColumn('profileMemberId');
        });
    }
}
