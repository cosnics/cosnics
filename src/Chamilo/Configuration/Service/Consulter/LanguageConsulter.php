<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\LanguageCacheDataPreLoader;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    protected LanguageCacheDataPreLoader $languageCacheDataPreLoader;

    public function __construct(LanguageCacheDataPreLoader $languageCacheDataPreLoader)
    {
        $this->languageCacheDataPreLoader = $languageCacheDataPreLoader;
    }

    public function getLanguageCacheDataPreLoader(): LanguageCacheDataPreLoader
    {
        return $this->languageCacheDataPreLoader;
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
        return $this->getLanguageCacheDataPreLoader()->getLanguages();
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
