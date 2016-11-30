<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';
require APPPATH . '/libraries/exceptions/HttpNotFoundException.php';
require APPPATH . '/libraries/exceptions/DbNotFoundException.php';

class RatingController extends RestController {

	public function getRatings($movie_id)
	{
		try
		{
			if($this->input->get("type") == "custom_avg")
			{
				$this->load->model('Rating');
				$this->Rating->getAVGRating($movie_id);
				$rating = $this->Rating->mapToArray();
				$this->response($rating, 200);
			}
			else
			{
				throw new HttpNotFoundException();
			}
		}
		catch(DbNotFoundException $e)
		{
			$this->response(null, null, new HttpNotFoundException("No Rating found for movie_id "));
		}
	}

	public function postCustomRating($movie_id)
	{
		$this->load->model('Rating');
		$rating_json = json_decode(trim(file_get_contents('php://input')), true);
		$this->Rating->setMovieId($movie_id);
		$this->Rating->setUserName($rating_json["user_name"]);
		$this->Rating->setRating($rating_json["rating"]);
		$this->Rating->setText($rating_json["text"]);
		try
		{
			$rating = $this->Rating->insertRating();
		}
		catch(Exception $e)
		{

		}

		$this->response($rating, 201);
	}
}