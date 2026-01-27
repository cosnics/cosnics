<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition;

use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator
{
    public const CONDITION_CLASS = NotCondition::class;

    public function translate(
        QueryBuilder $querybuilder, NotCondition $notCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = 'NOT (';
        $string[] = $this->getConditionPartTranslatorService()->translate(
            $querybuilder, $notCondition->getCondition(), $enableAliasing
        );
        $string[] = ')';

        return implode('', $string);
    }
}
