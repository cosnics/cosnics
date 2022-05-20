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
class NotConditionTranslator extends ConditionTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\NotCondition
     */
    public function getCondition(): ConditionPart
    {
        return parent::getCondition();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $string = [];

        $string[] = 'NOT (';
        $string[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getCondition()->getCondition(), $enableAliasing
        );
        $string[] = ')';

        return implode('', $string);
    }
}
