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

use Config;
use Lang;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class ProfileEditComposer extends AbstractDeepLinksComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data  = $view->getData();
        $user  = $data['user'];
        $login = $user->login;

        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks('users/'.$login.'/edit'));
        $view->with('weeklyNewsletter', $user->isSubscribedToNewsletter('weekly'));
        $view->with('dailyNewsletter', $user->isSubscribedToNewsletter('daily'));
        $view->with('colorsAvailable', $this->createSelectColorsData());
        $view->with('possibleNewslettersTypes', Newsletter::getPossibleTypes());
    }

    /**
     * Create an array like ['blue' => 'Blue', 'red' => 'Red'].
     *
     * @return array
     */
    private function createSelectColorsData()
    {
        // Create an array like
        // ['blue' => 'Blue', 'red' => 'Red']
        $colorsInConf = Config::get('app.users.colorsAvailableQuotesPublished');
        $func         = function ($colorName) {
            return Lang::get('colors.'.$colorName);
        };
        $colorsAvailable = array_combine($colorsInConf, array_map($func, $colorsInConf));

        return $colorsAvailable;
    }
}
