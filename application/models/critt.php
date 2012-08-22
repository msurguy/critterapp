<?php

class Critt extends Eloquent 
{
	public function user(){
		return $this -> belongs_to('User');
	}
}