<?php namespace TeenQuotes\Quotes\Controllers;

use BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;

class FavoriteQuoteController extends BaseController {

	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\QuotesFavoriteController
	 */
	private $api;

	public function __construct()
	{
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesFavoriteController');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($quote_id)
	{
		if (Request::ajax()) {

			$user = Auth::user();
			$data = [
				'quote_id' => $quote_id,
				'user_id'  => $user->id,
			];

			$validator = Validator::make($data, FavoriteQuote::$rulesAddFavorite);
			// FIXME : We can optimize because we will make a lot of queries
			// The validator will check for the existence of the user and will fetch the quote
			// These queries will be done 2 times
			$quote = Quote::find($data['quote_id']);

			// Check if the form validates with success.
			if ($validator->passes() AND ! is_null($quote) AND $quote->isPublished()) {

				// Try to find if the user has this quote in favorite from cache
				if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
					$alreadyFavorite = in_array($data['quote_id'], Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']));
				else {
					$favorite = FavoriteQuote::where('quote_id', '=' , $data['quote_id'])
						->where('user_id', '=' , $data['user_id'])
						->count();
					$alreadyFavorite = ($favorite === 1);
				}

				// Oops, the quote was already in its favorite
				if ($alreadyFavorite)
					return Response::json([
						'success'         => false,
						'alreadyFavorite' => true
					]);

				// Call the API to store the favorite
				$response = $this->api->postFavorite($quote_id, false);
				
				if ($response->getStatusCode() == 201)
					return Response::json(['success' => true], 200);				
			}

			return Response::json([
				'success' => false, 
				'errors'  => $validator->getMessageBag()->toArray()
			]);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($quote_id)
	{
		if (Request::ajax()) {

			$user = Auth::user();
			$data = [
				'quote_id' => $quote_id,
				'user_id'  => $user->id,
			];

			$validator = Validator::make($data, FavoriteQuote::$rulesRemoveFavorite);

			// Check if the form validates with success.
			if ($validator->passes()) {

				// Call the API to delete the favorite
				$response = $this->api->deleteFavorite($quote_id, false);
								
				if ($response->getStatusCode() == 200)
					return Response::json(['success' => true], 200);
			}

			return Response::json([
				'success' => false,
				'errors'  => $validator->getMessageBag()->toArray()
			]);
		}
	}
}