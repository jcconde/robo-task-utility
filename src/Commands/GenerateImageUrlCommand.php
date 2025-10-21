<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

namespace Juanca\RoboTaskUtility\Commands;

use Juanca\RoboTaskUtility\Services\ImageUrl\ImageUrlGenerator;
use Juanca\RoboTaskUtility\Services\ImageUrl\Provider\PexelsProvider;
use Robo\Tasks;

/**
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
class GenerateImageUrlCommand extends Tasks
{
    /**
     * @param array $opts
     * @return void
     * @command generate:image-url
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function generate(array $opts = ['category' => '', 'limit' => 2]): void
    {
        $logger = $this->getContainer()->get('logger');
        $config = $this->getContainer()->get('config');

        $categories = $this->sanitizeCategories($opts['category'] ?? '');

        $limit = (int)($opts['limit'] ?? 2);
        $generator = new ImageUrlGenerator($logger, [
            new PexelsProvider($logger, $config->get('pexels.api_key'))
        ]);

        $results = [];
        foreach ($categories as $category) {
            $this->say(sprintf('Generating images for category: %s', $category));
            $results[] = $generator->generate($category, $limit);
            $this->say("executed");
        }

        array_walk($results, function ($result) {
            $this->say(reset($result));
        });
    }

    /**
     * @param string $category
     * @return array|string[]
     */
    private function sanitizeCategories(string $category): array
    {
        $categoriesSanitized = explode(',', $category);
        $categoriesSanitized = array_map('trim', $categoriesSanitized);
        return array_filter($categoriesSanitized);
    }
}
