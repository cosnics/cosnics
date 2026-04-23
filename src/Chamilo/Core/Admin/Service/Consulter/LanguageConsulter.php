<?php
namespace Chamilo\Core\Admin\Service\Consulter;

use Chamilo\Core\Admin\Storage\Repository\LanguageRepository;

/**
 * @package Chamilo\Core\Admin\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    public function __construct(protected LanguageRepository $languageRepository)
    {
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languageRepository->findLanguagesAsArray();
    }

    /**
     * @return string[]
     */
    public function getOtherLanguages(string $isocodeToExclude): array
    {
        $languages = [];

        foreach ($this->getLanguages() as $isocode => $language) {
            if ($isocode !== $isocodeToExclude) {
                $languages[$isocode] = $language;
            }
        }

        return $languages;
    }
}
