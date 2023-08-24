<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoMetaTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_meta_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('canonical_condition_name',255)->nullable()->comment = 'Reference to canonicalName column of conditions table';
            $table->string('canonical_therapy_name',255)->nullable()->comment = 'Reference to canonicalName column of therapy table';
            
            $table->longText('title')->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_news_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('og_title')->nullable();
            $table->longText('og_description')->nullable();
            
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_meta_tags');
    }
}
