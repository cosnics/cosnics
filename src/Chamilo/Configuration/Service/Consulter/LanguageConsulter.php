<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Libraries\Cache\Interfaces\DataLoaderInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    protected DataLoaderInterface $dataLoader;

    public function __construct(DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function getDataLoader(): DataLoaderInterface
    {
        return $this->dataLoader;
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
        return $this->getDataLoader()->readData();
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
