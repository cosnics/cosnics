<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

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
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPartTranslator::translate()
     */
    public function translate()
    {
        $values = $this->getCondition()->get_values();

        if (! is_array($values))
        {
            if (is_scalar($values))
            {
                $values = array($values);
            }
            elseif (is_null($values))
            {
                $values = array();
            }
            else
            {
                throw new \InvalidArgumentException(
                    'An InCondition only accepts an array or a scalar as input for the values');
            }
        }

        if (count($values) > 0)
        {
            $where_clause = array();

            $where_clause[] = $this->getConditionPartTranslatorService()->translateConditionPart(
                $this->getDataClassDatabase(),
                $this->getCondition()->get_name()) . ' IN (';

            $placeholders = array();

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
            $value = 'true = false';
        }

        return $value;
    }
}
