<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Condition;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Condition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InConditionTranslator extends ConditionTranslator
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\ConditionTranslator::translate()
     */
    public function translate()
    {
        $values = $this->get_condition()->get_values();

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

            $where_clause[] = ConditionVariableTranslator :: render($this->get_condition()->get_name()) . ' IN (';

            $placeholders = array();

            foreach ($values as $value)
            {
                $placeholders[] = Database :: quote($value);
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
