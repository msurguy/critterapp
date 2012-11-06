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
		// Add admin user
		DB::table('users')->insert(array(
		    'username'  => 'admin',
		    'password'  => Hash::make('password'),
		    'name'  => 'Admin User'
		));
	}

	// on migration rollback drop the users table
	public function down()
	{
		Schema::drop('users');
	}

}