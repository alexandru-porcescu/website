<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Composers;

use Lang;
use Route;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;
use URL;

class WelcomeComposer extends AbstractDeepLinksComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $viewData = $view->getData();
        $login    = $viewData['user']->login;
        $type     = $viewData['type'];

        $welcomeText = Lang::get('users.newUserWelcomeProfile', ['login' => $login]);

        $updateProfileTitle   = Lang::get('users.newUserTutorialProfileTitle');
        $updateProfileContent = Lang::get('users.newUserTutorialProfileContent', ['url' => URL::route('users.edit', $login)]);

        $addingQuoteTitle   = Lang::get('users.newUserTutorialAddingQuoteTitle');
        $addingQuoteContent = Lang::get('users.newUserTutorialAddingQuoteContent', ['url' => URL::route('addquote')]);

        $addingFavoritesTitle   = Lang::get('users.newUserTutorialFavoritesTitle');
        $addingFavoritesContent = Lang::get('users.newUserTutorialFavoritesContent');

        $editSettingsTitle   = Lang::get('users.newUserTutorialSettingsTitle');
        $editSettingsContent = Lang::get('users.newUserTutorialSettingsContent', ['url' => URL::route('users.edit', $login).'#edit-settings']);

        // Content
        $view->with('welcomeText', $welcomeText);
        $view->with('updateProfileTitle', $updateProfileTitle);
        $view->with('updateProfileContent', $updateProfileContent);
        $view->with('addingQuoteTitle', $addingQuoteTitle);
        $view->with('addingQuoteContent', $addingQuoteContent);
        $view->with('addingFavoritesTitle', $addingFavoritesTitle);
        $view->with('addingFavoritesContent', $addingFavoritesContent);
        $view->with('editSettingsTitle', $editSettingsTitle);
        $view->with('editSettingsContent', $editSettingsContent);

        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks('users/'.$login.'/'.$type));
    }
}
