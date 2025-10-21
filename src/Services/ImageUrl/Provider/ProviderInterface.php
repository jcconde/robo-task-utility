<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

namespace Juanca\RoboTaskUtility\Services\ImageUrl\Provider;

interface ProviderInterface
{
    /**
     * Generate an image url for the given category.
     *
     * @param string $query
     * @param int $imagePerPage
     * @param int $page
     * @return array<int, string>
     */
    public function generate(string $query, int $imagePerPage = 1, int $page = 1): array;
}
