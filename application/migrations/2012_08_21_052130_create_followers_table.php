<?php

class Create_Followers_Table {

	public function up()
	{
		Schema::create('followers', function($table) {
			$table->integer('user_id');
			$table->integer('following_id');
			$table->primary(array('user_id', 'following_id'));
			//$table->foreign('user_id')->references('id')->on('users')->on_delete('restrict');
		});
	}

	public function down()
	{
		Schema::drop('followers');
	}

}