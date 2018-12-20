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
     * @return string[][]
     */
    public function getLanguages()
    {
        return $this->getData();
    }

    /**
     *
     * @param string $isocode
     * @return string
     */
    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();
        return $languages[$isocode];
    }
}
