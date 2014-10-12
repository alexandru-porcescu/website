<?php

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;
use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Models\Relations\UserTrait as UserRelationsTrait;
use TeenQuotes\Models\Scopes\UserTrait as UserScopesTrait;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Settings\Models\Setting;

class User extends Eloquent implements UserInterface, RemindableInterface {
	
	use PresentableTrait, RemindableTrait, UserTrait, UserRelationsTrait, UserScopesTrait;

	protected $presenter = 'TeenQuotes\Presenters\UserPresenter';

	/**
	 * The database table used by the model.
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 * @var array
	 */
	protected $hidden = ['password', 'ip', 'hide_profile', 'remember_token', 'updated_at', 'avatar', 'security_level', 'notification_comment_quote'];

	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = ['profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin'];

	/**
	 * Adding attributes to the object. These attributes need extra DB queries
	 * @var array
	 */
	public static $appendsFull = ['total_comments', 'favorite_count', 'added_fav_count', 'published_quotes_count', 'is_subscribed_to_daily', 'is_subscribed_to_weekly'];

	/**
	 * The validation rules when updating a profile
	 * @var array
	 */
	public static $rulesUpdate = [
		'gender'    => 'in:M,F',
		'birthdate' => 'date_format:"Y-m-d"',
		'country'   => 'exists:countries,id',
		'city'      => '',
		'avatar'    => 'image|max:500',
		'about_me'  => 'max:500',
	];

	/**
	 * The validation rules when updating a password
	 * @var array
	 */
	public static $rulesUpdatePassword = [
		'password' => 'required|min:6|confirmed',
	];

	/**
	 * The validation rules when deleting an account
	 * @var array
	 */
	public static $rulesDestroy = [
		'password'            => 'required|min:6',
		'delete-confirmation' => 'in:DELETE'
	];

	/**
	 * The validation rules when signing up
	 * @var array
	 */
	public static $rulesSignup = [
		'password' => 'required|min:6',
		'login'    => 'required|alpha_dash|unique:users,login|min:3|max:20',
		'email'    => 'required|email|unique:users,email',
	];

	/**
	 * The validation rules when signing in
	 * @var array
	 */
	public static $rulesSignin = [
		'password' => 'required|min:6',
		'login'    => 'required|alpha_dash|exists:users,login|min:3|max:20',
	];

	/**
	 * The name of the key to store in cache. Describes quotes published by a user
	 * @var array
	 */
	public static $cacheNameForPublished = 'quotes_published_';

	/**
	 * The name of the key to store in cache. Describes quotes favorited by a user
	 * @var array
	 */
	public static $cacheNameForFavorited = 'quotes_favorited_';

	/**
	 * The name of the key to store in cache. Describes the number of quotes published by a user
	 * @var array
	 */
	public static $cacheNameForNumberQuotesPublished = 'number_quotes_published_';

	/**
	 * The name of the key to store in cache. Describes the colors used for the published quotes of the user
	 * @var array
	 */
	public static $cacheNameForColorsQuotesPublished = 'colors_quotes_published_';

	public function getProfileHiddenAttribute()
	{
		return $this->isHiddenProfile();
	}

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	/**
	 * Tells if the user wants to hide his profile
	 * @return boolean true if we should hide his profile, false otherwise
	 */
	public function isHiddenProfile()
	{
		return $this->hide_profile == 1;
	}

	/**
	 * Tells if a user is a male
	 * @return boolean
	 */
	public function isMale()
	{
		return $this->gender == 'M';
	}

	/**
	 * Tells if a user is a female
	 * @return boolean
	 */
	public function isFemale()
	{
		return ! $this->isMale();
	}

	public function getWantsNotificationCommentQuoteAttribute()
	{
		return $this->wantsEmailComment();
	}

	public function getIsAdminAttribute()
	{
		return $this->security_level == 1;
	}

	public function getTotalComments()
	{
		return $this->comments()->count();
	}

	public function getFavoriteCount()
	{
		return $this->favoriteQuotes()->count();
	}

	/**
	 * Tells if the user is subscribed to the daily or the weekly newsletter
	 * @var string $type The type of the newsletter : weekly|daily
	 * @return boolean true if subscribed, false otherwise
	 */
	public function isSubscribedToNewsletter($type)
	{
		return (Newsletter::forUser($this)->type($type)->count() > 0);
	}

	public function getIsSubscribedToDaily()
	{
		return $this->isSubscribedToNewsletter('daily');
	}

	public function getIsSubscribedToWeekly()
	{
		return $this->isSubscribedToNewsletter('weekly');
	}

	public function getAddedFavCount()
	{
		$idsQuotesPublished = Quote::forUser($this)
			->published()
			->lists('id');

		if (empty($idsQuotesPublished))
			return 0;
		
		return FavoriteQuote::whereIn('quote_id', $idsQuotesPublished)->count();
	}

	public function getPublishedQuotesCount()
	{
		return Quote::forUser($this)
			->published()
			->count();
	}

	/**
	 * Tells if the user wants to receive an email when a comment is
	 * added on one of its quotes
	 * @return boolean true if we should send an email, false otherwise
	 */
	public function wantsEmailComment()
	{
		return $this->notification_comment_quote == 1;
	}

	/**
	 * Returns the old hash of a password. It was used in Teen Quotes v2
	 * @var array $data The data. We need a login and a password
	 * @return string The corresponding hash that was used in Teen Quotes v2
	 */
	public static function oldHashMethod($data)
	{
		// This is legacy code. This hash method was used in 2005 by Mangos...
		// I feel a bit old and stupid right now.
		return sha1(strtoupper($data['login']).':'.strtoupper($data['password']));
	}

	/**
	 * Get the array of colors to use for the published quotes of the user
	 * @return string The name of the color to use for the user's instance. Example: blue|red|orange
	 */
	public function getColorsQuotesPublished()
	{
		// If we have something in cache, return it immediately
		if (Cache::has(self::$cacheNameForColorsQuotesPublished.$this->id))
			return Cache::get(self::$cacheNameForColorsQuotesPublished.$this->id);

		$confColor = Setting::where('user_id', '=', $this->id)
			->where('key', '=', 'colorsQuotesPublished')
			->first();

		// Set colors to put in cache for the user
		if (is_null($confColor))
			$toPut = Config::get('app.users.defaultColorQuotesPublished');
		else
			$toPut = $confColor->value;

		// Store in cache
		Cache::put(self::$cacheNameForColorsQuotesPublished.$this->id, $toPut, Carbon::now()->addMinutes(10));

		return $toPut;
	}

	public function getURLAvatarAttribute()
	{
		return $this->present()->avatarLink;
	}

	/**
	 * Returns the array of the ID of the quotes in the favorites of the user
	 * @return array 
	 */
	public function arrayIDFavoritesQuotes()
	{
		$expiresAt = Carbon::now()->addMinutes(10);
		$user = $this;

		$arrayIDFavoritesQuotesForUser = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.$this->id, $expiresAt, function() use ($user)
		{
			return FavoriteQuote::forUser($user)
				->select('quote_id')
				->orderBy('id', 'DESC')
				->get()
				->lists('quote_id');
		});

		return $arrayIDFavoritesQuotesForUser;
	}

	public function hasPublishedQuotes()
	{
		return $this->getPublishedQuotesCount() > 0;
	}

	public function hasFavoriteQuotes()
	{
		return $this->getFavoriteCount() > 0;
	}

	public function hasPostedComments()
	{
		return $this->getTotalComments() > 0;
	}
}