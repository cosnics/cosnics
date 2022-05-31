<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;

/**
 *
 * @package Chamilo\Configuration\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findLanguagesAsRecords()
    {
        return $this->getDataClassRepository()->records(
            Language::class,
            new RecordRetrievesParameters(
                new RetrieveProperties(array(new PropertiesConditionVariable(Language::class)))));
    }

    /**
     *
     * @return boolean
     */
    public function clearLanguageCache()
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(Language::class);
    }
}