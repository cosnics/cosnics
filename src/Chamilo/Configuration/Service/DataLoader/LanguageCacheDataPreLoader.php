<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Configuration\Storage\DataClass\LanguageCodeEnum;
use Chamilo\Configuration\Storage\Repository\LanguageRepository;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageCacheDataPreLoader implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    protected LanguageRepository $languageRepository;

    public function __construct(AdapterInterface $cacheAdapter, LanguageRepository $languageRepository)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @return string[]
     */
    protected function getDataForCache(): array
    {
        $languageValues = [];
        $languages = $this->getLanguageRepository()->findLanguages();

        foreach ($languages as $language)
        {
            $languageValues[$language->getCode(LanguageCodeEnum::ISO_639_1)] = $language->getName();
        }

        return $languageValues;
    }

    public function getLanguageRepository(): LanguageRepository
    {
        return $this->languageRepository;
    }

    public function getLanguages(): array
    {
        return $this->loadCacheData();
    }
}
