<?php
namespace Chamilo\Libraries\Storage\Service\Condition;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Service\ConditionTranslator;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service\Condition
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator implements ConditionTranslatorInterface
{
    public function getConditionClassName(): string
    {
        return NotCondition::class;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, NotCondition $notCondition, ?bool $enableAliasing = true
    ): string
    {
        $string = [];

        $string[] = 'NOT';
        $string[] = '(';
        $string[] = $this->getConditionTranslatorCollection()->translate(
            $querybuilder, $notCondition->getCondition(), $enableAliasing
        );
        $string[] = ')';

        return implode(' ', $string);
    }
}
