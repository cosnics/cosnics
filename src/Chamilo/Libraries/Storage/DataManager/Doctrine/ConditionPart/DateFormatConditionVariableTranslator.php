<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DateFormatConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\DateFormatConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $strings = [];

        $strings[] = 'FROM_UNIXTIME';

        $strings[] = '(';

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getConditionVariable()->getConditionVariable(), $enableAliasing
        );
        $strings[] = ', ';
        $strings[] = "'" . $this->getConditionVariable()->getFormat() . "'";
        $strings[] = ')';

        if ($this->getConditionVariable()->getAlias())
        {
            return implode('', $strings) . ' AS ' . $this->getConditionVariable()->getAlias();
        }
        else
        {
            return implode('', $strings);
        }
    }
}
