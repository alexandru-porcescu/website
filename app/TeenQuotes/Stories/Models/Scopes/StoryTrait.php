<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Stories\Models\Scopes;

trait StoryTrait
{
    public function scopeOrderDescending($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }
}
