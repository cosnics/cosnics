<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Repository\LanguageRepository;
use Chamilo\Configuration\Storage\DataClass\Language;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageLoader implements CacheableDataLoaderInterface
{

    /**
     *
     * @var \Chamilo\Configuration\Repository\LanguageRepository
     */
    private $registrationRepository;

    /**
     *
     * @param \Chamilo\Configuration\Repository\LanguageRepository $registrationRepository
     */
    public function __construct(LanguageRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Repository\LanguageRepository
     */
    public function getLanguageRepository()
    {
        return $this->registrationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Repository\LanguageRepository $registrationRepository
     */
    public function setLanguageRepository($registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        $languages = array();
        $languageRecords = $this->getLanguageRepository()->findLanguagesAsRecords();

        foreach ($languageRecords as $languageRecord)
        {
            $languages[$languageRecord[Language::PROPERTY_ISOCODE]] = $languageRecord[Language::PROPERTY_ORIGINAL_NAME];
        }

        return $languages;
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(__CLASS__);
    }
}
