<?php

use Fuel\Core\Presenter;
use Fuel\Core\Uri;

class Presenter_Menu extends Presenter
{
	public function view()
	{
		$this->current_url = Uri::segment(1);
		$this->tabs = array();
		
//		array_push($this->tabs, array('url' => 'home', 'name' => 'Start'));
		array_push($this->tabs, array('url' => 'book', 'name' => 'Książki'));
		
		if (Auth::check()) {
			if (Auth::has_access('reader.access'))
				array_push($this->tabs, array('url' => 'reader', 'name' => 'Czytelnicy'));
			
			if (Auth::has_access('right.admin'))
				array_push($this->tabs, array('url' => 'user', 'name' => 'Użytkownicy'));
			
			array_push($this->tabs, array('url' => 'account', 'name' => 'Moje konto'));
			array_push($this->tabs, array('url' => 'logout', 'name' => 'Wyloguj'));
			
		} else {
			array_push($this->tabs, array('url' => 'newaccount', 'name' => 'Nowe konto'));
			array_push($this->tabs, array('url' => 'login', 'name' => 'Zaloguj'));
		}
	}
}
