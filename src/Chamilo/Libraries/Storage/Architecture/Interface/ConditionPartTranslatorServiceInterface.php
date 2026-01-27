<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConditionPartTranslatorServiceInterface
{
    public function translate(
        QueryBuilder $queryBuilder, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string;
}