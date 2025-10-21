<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

namespace Juanca\RoboTaskUtility\Services\ImageUrl\Provider;

use Psr\Log\LoggerInterface;
use WBW\Library\Pexels\Api\RequestInterface;
use WBW\Library\Pexels\Model\Photo;
use WBW\Library\Pexels\Model\Source;
use WBW\Library\Pexels\Provider\ApiProvider;
use WBW\Library\Pexels\Request\SearchPhotosRequest;
use WBW\Library\Pexels\Response\PhotosResponse;

class PexelsProvider implements ProviderInterface
{
    private SearchPhotosRequest $request;
    private ApiProvider $provider;
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param string $apiKey
     */
    public function __construct(
        LoggerInterface $logger,
        string $apiKey
    ) {
        $this->logger = $logger;
        $this->request = new SearchPhotosRequest();
        $this->provider = new ApiProvider($apiKey, $this->logger);
    }

    /**
     * @inheritdoc
     */
    public function generate(string $query, int $imagePerPage = 1, int $page = 1): array
    {
        $this->request->setQuery($query);
        $this->request->setPerPage($imagePerPage);
        $this->request->setPage($page);
        $this->request->setOrientation(RequestInterface::ORIENTATION_LANDSCAPE); // Optional
        $this->request->setSize(RequestInterface::SIZE_LARGE); // Optional
        $this->request->setLocale(RequestInterface::LOCALE_EN_US); // Optional
        /** @var PhotosResponse $response */
        $response = $this->provider->sendRequest($this->request);

        $urls = [];
        /** @var Photo $current */
        foreach ($response->getPhotos() as $current) {
            try {
                /** @var Source $source */
                $source = $current->getSrc();
                $urls[] = $source->getLarge();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $urls;
    }
}
