<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\Repository\LanguageRepository;

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
     * @var \Chamilo\Configuration\Storage\Repository\LanguageRepository
     */
    private $registrationRepository;

    /**
     *
     * @param \Chamilo\Configuration\Storage\Repository\LanguageRepository $registrationRepository
     */
    public function __construct(LanguageRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Storage\Repository\LanguageRepository
     */
    public function getLanguageRepository()
    {
        return $this->registrationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Storage\Repository\LanguageRepository $registrationRepository
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
        $languages = [];
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

    /**
     *
     * @see \Chamilo\Configuration\Interfaces\DataLoaderInterface::clearData()
     */
    public function clearData()
    {
        return $this->getLanguageRepository()->clearLanguageCache();
    }
}
