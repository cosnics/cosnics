<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class NotConditionTranslator extends ConditionTranslator
{

    /**
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(bool $enableAliasing = true)
    {
        $string = array();

        $string[] = 'NOT (';
        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->get_condition(), $enableAliasing
        );
        $string[] = ')';

        return implode('', $string);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\NotCondition
     */
    public function getCondition()
    {
        return parent::getCondition();
    }
}
