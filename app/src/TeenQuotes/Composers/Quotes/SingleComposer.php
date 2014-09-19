<?php namespace TeenQuotes\Composers\Quotes;

use Illuminate\Support\Facades\URL;
use JavaScript;
use TeenQuotes\Composers\AbstractDeepLinksComposer;

class SingleComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		JavaScript::put([
			'urlFavoritesInfo' => URL::route('quotes.favoritesInfo'),
		]);
	}
}