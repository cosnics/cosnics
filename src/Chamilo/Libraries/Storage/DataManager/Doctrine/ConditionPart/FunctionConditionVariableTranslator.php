<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariableTranslator extends ConditionVariableTranslator
{

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable
     */
    public function getConditionVariable(): ConditionPart
    {
        return parent::getConditionVariable();
    }

    public function translate(?bool $enableAliasing = true): string
    {
        $strings = [];
        switch ($this->getConditionVariable()->getFunction())
        {
            case FunctionConditionVariable::SUM :
                $strings[] = 'SUM';
                break;
            case FunctionConditionVariable::COUNT :
                $strings[] = 'COUNT';
                break;
            case FunctionConditionVariable::MIN :
                $strings[] = 'MIN';
                break;
            case FunctionConditionVariable::MAX :
                $strings[] = 'MAX';
                break;
            case FunctionConditionVariable::DISTINCT :
                $strings[] = 'DISTINCT';
                break;
            case FunctionConditionVariable::AVERAGE :
                $strings[] = 'AVG';
                break;
        }

        if ($this->getConditionVariable()->getFunction() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = '(';
        }
        else
        {
            $strings[] = ' ';
        }

        $strings[] = $this->getConditionPartTranslatorService()->translate(
            $this->getDataClassDatabase(), $this->getConditionVariable()->getConditionVariable(), $enableAliasing
        );

        if ($this->getConditionVariable()->getFunction() !== FunctionConditionVariable::DISTINCT)
        {
            $strings[] = ')';
        }

        if ($this->getConditionVariable()->getAlias())
        {
            $value = implode('', $strings) . ' AS ' . $this->getConditionVariable()->getAlias();
        }
        else
        {
            $value = implode('', $strings);
        }

        return $value;
    }
}
