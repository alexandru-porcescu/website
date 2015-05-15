<?php

namespace TeenQuotes\Auth\Composers;

use JavaScript;

class SigninComposer
{
    public function compose($view)
    {
        $data = $view->getData();

        if ($data['requireLoggedInAddQuote']) {
            JavaScript::put([
                'eventCategory' => 'addquote',
                'eventAction'   => 'not-logged-in',
                'eventLabel'    => 'signin-page',
            ]);
        }
    }
}
