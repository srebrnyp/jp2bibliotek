<?php


use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Auth\Auth;
use Model\Book;

use Fuel\Core\Pagination;

class Controller_Book_List extends Controller_Template
{
	public function action_index()
	{
		$title = Input::get('title');
		$author = Input::get('author');
		$type = Input::get('type');

		$books_count = Model_Book::query_like($title, $author, $type)->count();
		
		$num_links = 8;
		$show_first_and_last =  ($books_count / 100) > $num_links;
		
		$pagination = Pagination::forge('mypagination', 
				array(
						'total_items'    => $books_count,
						'per_page'       => 100,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
		
		switch (Input::get('by')) {
			case "author": $order_type = "authors.name"; break;
			case "title":  $order_type = "title"; break;
			default:       $order_type = DB::expr('LENGTH(tag), tag'); break;
		}
		
		$books = 
			Model_Book::query_like($title, $author, $type)
				->order_by($order_type)
				->get();

		$books = array_slice($books, 
							 $pagination->offset,
							 $pagination->per_page);
		
		/* 
		 * Have to go through book authors,
		 * since after changing author number 
		 * in the edit, only one author per book
		 * appears. This is a sort of fix to this issue.
		 */
		foreach ($books as $book)
			$book->get_authors();
			
		$data['pagination'] = $pagination;
		$data['books'] = $books;
		
		$this->template->short_head = true;
		$this->template->title = 'Wyszukane ksiażki ('. $books_count. ')';
		$this->template->content = View::forge('book/list')
			->set('books', $books)
			->set('pagination', $pagination);
	}
}
