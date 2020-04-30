<?php
namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;

/**
 *
 * @package Chamilo\Configuration\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationRepository
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
     * @return boolean
     */
    public function clearTemplateRegistrationCache()
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(TemplateRegistration::class);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     * @throws \Exception
     */
    public function findTemplateRegistrations()
    {
        return $this->getDataClassRepository()->retrieves(
            TemplateRegistration::class, new DataClassRetrievesParameters()
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     * @throws \Exception
     */
    public function findTemplateRegistrationsAsRecords()
    {
        return $this->getDataClassRepository()->records(
            TemplateRegistration::class, new RecordRetrievesParameters(
                new DataClassProperties(array(new PropertiesConditionVariable(TemplateRegistration::class)))
            )
        );
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
}