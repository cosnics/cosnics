<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StartsWithConditionTranslator extends PatternMatchConditionTranslator
{
    protected function getPattern(): string
    {
        return parent::getPattern() . '%';
    }
}
