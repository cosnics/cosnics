<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\LanguageCacheDataLoader;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    protected LanguageCacheDataLoader $languageCacheDataLoader;

    public function __construct(LanguageCacheDataLoader $languageCacheDataLoader)
    {
        $this->languageCacheDataLoader = $languageCacheDataLoader;
    }

    public function getLanguageCacheDataLoader(): LanguageCacheDataLoader
    {
        return $this->languageCacheDataLoader;
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
        return $this->getLanguageCacheDataLoader()->getLanguages();
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
