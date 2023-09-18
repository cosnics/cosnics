<?php
namespace Chamilo\Core\Metadata\Schema\Service;

use Chamilo\Core\Metadata\Schema\Storage\Repository\SchemaRepository;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Metadata\Schema\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaService
{
    protected SchemaRepository $schemaRepository;

    public function __construct(SchemaRepository $schemaRepository)
    {
        $this->schemaRepository = $schemaRepository;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Metadata\Storage\DataClass\Schema>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSchemasForCondition(?Condition $condition = null): ArrayCollection
    {
        return $this->getSchemaRepository()->findSchemasForCondition($condition);
    }

    public function getSchemaRepository(): SchemaRepository
    {
        return $this->schemaRepository;
    }

}