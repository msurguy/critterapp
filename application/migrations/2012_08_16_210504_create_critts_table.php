<?php

class Create_Critts_Table {

	public function up()
	{
		Schema::create('critts', function($table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->text('critt');
			$table->timestamps();
			//$table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
		});
	}

	// on migration rollback drop the critts table
	public function down()
	{
		Schema::drop('critts');
	}

}