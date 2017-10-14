<?php

use Message\Message;

class Controller_Borrow_Info extends Controller_Template
{
	public function before()
	{
		return parent::before();

		if (!Auth::has_access("book.borrow"))
			return Response::redirect('login');
	}

	/************************************************************************/
	private function query_string()
	{
		$query = Uri::build_query_string(Input::get());
		
		if (strlen($query) > 0)
			return  '?' . $query;
		else 
			return '';
	}
	
	/************************************************************************/
	public function action_id($id)
	{
		$borrow = Model_Borrow::find($id);
	
		if ($borrow == null)
			return Response::redirect('404');
	
		$button_list = array();
	
		if ($borrow->returned_at == 0)
			array_push($button_list,
					array('../return/' . $id . $this->query_string(), 'Zwróć',
							'onclick' => "return confirm ('Czy napewno zwrócić?')"
					));
	
		array_push($button_list, array('../edit/' . $id . $this->query_string(), 'Edytuj'));
		// back button goes to a list
		array_push($button_list, array('..' . $this->query_string(), 'Wstecz'));

		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', $button_list);

		$this->template->content = View::forge('borrow/borrowinfo')
			->set('borrow', $borrow);

		$this->template->content .= $buttons;
	}
	/************************************************************************/
	public function action_return($id)
	{
		$borrow = Model_Borrow::find($id);
	
		if ($borrow == null) {
			return Response::redirect('404');
		}
	
		$borrow->returned_at = time();
		$borrow->save();
			
		Response::redirect_back();
	}
	
	/************************************************************************/
	public function action_edit($id)
	{
		$borrow = Model_Borrow::query()->where('id', $id)->get_one();
	
		if ($borrow == null)
			return Response::redirect('404');
	
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
		$form->add('book_tag', 'Identyfikator książki', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('book', 'Tytuł książki', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('reader', 'Czytelnik', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('borrowed_at', 'Pożyczono dnia', array('class' => 'form-control', 'readonly' => 'readonly'));
		if ($borrow->returned_at != 0)
			$form->add('returned_at', 'Oddano dnia', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));

		if (Input::post()) {
			$borrow->comment = Input::post('comment');
			$borrow->save();
			Message::add_success('Wprowadzono zmiany');
		}

		$input = array (
				'book_tag' => $borrow->id,
				'book' => $borrow->book->title,
				'reader' => $borrow->reader->name,
				'borrowed_at' => Date::forge($borrow->borrowed_at)->format("%d.%m.%y"),
				'returned_at' => Date::forge($borrow->returned_at)->format("%d.%m.%y"),
				'comment' => $borrow->comment
		);

		$form->populate($input);

		$this->template->title = 'Edytuj komentarz';
		$this->template->content = $form;
		$this->template->content .= View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(array('../id/' . $id . $this->query_string(), 'Wstecz')));
	}
	
}