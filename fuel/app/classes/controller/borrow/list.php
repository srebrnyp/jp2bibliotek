<?php


class Controller_Borrow_List extends Controller_Template
{
	public function before()
	{
		return parent::before();
	
		if (!Auth::has_access("book.borrow"))
			return Response::redirect('login');
	}
	
	/************************************************************************/
	static private function query_string()
	{
		$query = Uri::build_query_string(Input::get());
	
		if (strlen($query) > 0)
			return  '?' . $query;
			else
				return '';
	}
	

	/************************************************************************/
	public function action_index()
	{
		$borrows_count = \Model_Borrow::all()->count();
	
		$num_links = 8;
		$show_first_and_last = ($borrows_count / 10) > $num_links;
	
		$pagination = Pagination::forge('mypagination',
				array(
						'total_items'    => $borrows_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
	
		$borrows = \Model_Borrow::all();
	
		$this->template->title = 'Pożyczone ('. $borrows->count(). ')';
		$this->template->content = View::forge('borrow/borrowlist')
			->set('borrows', $borrows->get())
			->set('pagination', $pagination)
			->set('current_view', $this->query_string());
	}
}