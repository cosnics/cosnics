<?php
namespace Chamilo\Configuration\Service;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter extends DataConsulter
{

    /**
     *
     * @param string $isocode
     *
     * @return string
     */
    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();

        return $languages[$isocode];
    }

    /**
     *
     * @return string[]
     */
    public function getLanguages()
    {
        return $this->getData();
    }

    /**
     * @param string $isocodeToExclude
     *
     * @return string[]
     */
    public function getOtherLanguages(string $isocodeToExclude)
    {
        $languages = array();

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
