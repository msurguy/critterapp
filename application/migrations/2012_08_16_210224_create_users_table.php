<?php

class Create_Users_Table {

	public function up()
	{
		Schema::create('users', function($table) {
			$table->increments('id');
			$table->string('username')-> unique();
			$table->string('password');
			$table->string('name');
			$table->timestamps();
		});
		DB::table('users')->insert(array(
		    'username'  => 'admin',
		    'password'  => Hash::make('password'),
		    'name'  => 'Pete Adminovich'
		));
	}

	public function down()
	{
		Schema::drop('users');
	}

}