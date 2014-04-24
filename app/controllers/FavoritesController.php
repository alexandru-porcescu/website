<?php

class FavoritesController extends \BaseController {
	
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

			$validator = Validator::make($data, FavoriteQuote::$rulesFavorite);

			// Check if the form validates with success.
			if ($validator->passes()) {
				
				$favorite = FavoriteQuote::where('quote_id', '=' , $data['quote_id'])->where('user_id', '=' , $data['user_id'])->first();
				
				// Oops, the quote was already in its favorite
				if (!is_null($favorite)) {
					return Response::json(['success' => false, 'alreadyFavorite' => true]);
				}
				else {
					// Store the favorite
					$favorite = new FavoriteQuote;
					$favorite->user_id = $data['user_id'];
					$favorite->quote_id = $data['quote_id'];
					$favorite->save();

					return Response::json(['success' => true], 200);
				}
				
			}
			// Errors
			else
				return Response::json(['success' => false, 'errors' => $validator->getMessageBag()->toArray()]);
		}
		// It was not an Ajax call
		else
			return Redirect::route('addquote')->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}