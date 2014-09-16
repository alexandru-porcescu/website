<?php

use TeenQuotes\Models\Relations\CommentTrait as CommentRelationsTrait;
use TeenQuotes\Models\Scopes\CommentTrait as CommentScopesTrait;

class Comment extends Toloquent {

	use CommentRelationsTrait, CommentScopesTrait;
	
	protected $fillable = [];

	protected $hidden = ['deleted_at', 'updated_at'];

	/**
	 * The validation rules when adding a comment
	 * @var array
	 */
	public static $rulesAdd = [
		'content'  => 'required|min:10|max:500',
		'quote_id' => 'required|exists:quotes,id',
	];

	public static $cacheNameQuotesPage = 'quotes_homepage_';

	public function isPostedBySelf()
	{
		if (Auth::check())
			return $this->user_id == Auth::id();

		return false;
	}

	public function isPostedByUser(User $u)
	{
		return $this->user_id == $u->id;
	}
}