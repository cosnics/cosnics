<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Libraries\Cache\DataConsulterTrait;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Interfaces\DataConsulterInterface;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter implements DataConsulterInterface
{
    use DataConsulterTrait;

    public function __construct(CacheDataLoaderInterface $dataLoader)
    {
        $this->$dataLoader = $dataLoader;
    }

    public function getLanguageNameFromIsocode(string $isocode): string
    {
        $languages = $this->getLanguages();

        return $languages[$isocode];
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getDataLoader()->loadCacheData();
    }

    /**
     * @param string $isocodeToExclude
     *
     * @return string[]
     */
    public function getOtherLanguages(string $isocodeToExclude): array
    {
        $languages = [];

        foreach ($this->getLanguages() as $isocode => $language)
        {
            if ($isocode !== $isocodeToExclude)
            {
                $languages[$isocode] = $language;
            }
        }

        return $languages;
    }
}
