<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface EvaluationEntityServiceInterface
{
    public function getEntitiesFromIds(array $entityIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters = null): RecordIterator;
    public function countEntitiesFromIds(array $entityIds, FilterParameters $filterParameters): int;
    public function getFieldMapper(): FieldMapper;
}
