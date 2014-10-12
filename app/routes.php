<?php
/*
|--------------------------------------------------------------------------
| Patterns
|--------------------------------------------------------------------------
|
*/
Route::pattern('display_type', 'favorites|comments');

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
*/

/* --- MAIN WEBSITE --- */
Route::group(['domain' => Config::get('app.domain')], function()
{
	/* --- SEARCH --- */
	Route::get('search', ['as' => 'search.form', 'uses' => 'SearchController@showForm']);
	Route::post('search', ['as' => 'search.dispatcher', 'uses' => 'SearchController@dispatcher']);
	Route::get('search/{query}', ['as' => 'search.results', 'uses' => 'SearchController@getResults']);

	/* --- USERS --- */
	Route::delete('users', ['as' => 'users.delete', 'before' => 'auth', 'uses' => 'UsersController@destroy']);
	Route::get("signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
	Route::get('users/{user_id}/{display_type?}', ['as' => 'users.show', 'uses' => 'UsersController@show']);
	Route::put('users/{user_id}/password', ['as' => 'users.password', 'uses' => 'UsersController@putPassword']);
	Route::put('users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => 'UsersController@putAvatar']);
	Route::put('users/{user_id}/settings', ['as' => 'users.settings', 'uses' => 'UsersController@putSettings']);
	Route::post('users/loginvalidator', ['as' => 'users.loginValidator', 'uses' => 'UsersController@postLoginValidator']);
	Route::resource('users', 'UsersController', ['only' => ['index', 'store', 'edit', 'update']]);
});