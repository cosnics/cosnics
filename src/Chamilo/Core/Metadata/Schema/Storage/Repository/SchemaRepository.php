<?php
namespace Chamilo\Core\Metadata\Schema\Storage\Repository;

use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Metadata\Schema\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaRepository
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Metadata\Storage\DataClass\Schema>
     */
    public function findSchemasForCondition(?Condition $condition = null): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(Schema::class, new DataClassRetrievesParameters($condition));
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

}