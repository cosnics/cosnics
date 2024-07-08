<?php
namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;

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
     * @var \Chamilo\Libraries\Storage\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return bool
     */
    public function clearTemplateRegistrationCache()
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncateClass(
            TemplateRegistration::class
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     * @throws \Exception
     */
    public function findTemplateRegistrations()
    {
        return $this->getDataClassRepository()->retrieves(TemplateRegistration::class);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     * @throws \Exception
     */
    public function findTemplateRegistrationsAsRecords()
    {
        return $this->getDataClassRepository()->records(
            TemplateRegistration::class, new StorageParameters(
                retrieveProperties: new RetrieveProperties(
                    [new PropertiesConditionVariable(TemplateRegistration::class)]
                )
            )
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }
}