<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class InConditionTranslator extends ConditionTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\InCondition
     */
    public function getCondition(): ConditionPart
    {
        return parent::getCondition();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $values = $this->getCondition()->getValues();

        if (count($values) > 0)
        {
            $where_clause = [];

            $where_clause[] = $this->getConditionPartTranslatorService()->translate(
                    $this->getDataClassDatabase(), $this->getCondition()->getConditionVariable(), $enableAliasing
                ) . ' IN (';

            $placeholders = [];

            foreach ($values as $value)
            {
                $placeholders[] = $this->getDataClassDatabase()->quote($value);
            }

            $where_clause[] = implode(',', $placeholders);
            $where_clause[] = ')';

            $value = implode('', $where_clause);
        }
        else
        {
            $value = '1 = 0';
        }

        return $value;
    }
}
