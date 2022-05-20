<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StaticConditionVariableTranslator extends ConditionVariableTranslator
{

    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, StaticConditionVariable $staticConditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        $value = $staticConditionVariable->getValue();

        if ($staticConditionVariable->getQuote())
        {
            $value = $dataClassDatabase->quote($value);
        }

        return $value;
    }
}
