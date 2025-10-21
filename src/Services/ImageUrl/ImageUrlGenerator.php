<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

namespace Juanca\RoboTaskUtility\Services\ImageUrl;

use Juanca\RoboTaskUtility\Services\ImageUrl\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

class ImageUrlGenerator
{
    /**
     * @var array<int, ProviderInterface>
     */
    private array $providers;
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param array<int, ProviderInterface> $providers
     */
    public function __construct(LoggerInterface $logger, array $providers)
    {
        $this->logger = $logger;
        $this->providers = $providers;
    }

    /**
     * @param string $query
     * @param int $limit
     * @return array<int, string>
     */
    public function generate(string $query, int $limit): array
    {
        $this->logger->debug('Generating image url');
        $provider = $this->providers[array_rand($this->providers)];
        return $provider->generate($query, $limit);
    }
}
