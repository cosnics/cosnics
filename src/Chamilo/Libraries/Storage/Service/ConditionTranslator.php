<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorCollection;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorCollection;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionTranslator
{
    protected ConditionTranslatorCollection $conditionTranslatorCollection;

    protected ConditionVariableTranslatorCollection $conditionVariableTranslatorCollection;

    public function __construct(
        ConditionTranslatorCollection $conditionTranslatorCollection,
        ConditionVariableTranslatorCollection $conditionVariableTranslatorCollection
    )
    {
        $this->conditionTranslatorCollection = $conditionTranslatorCollection;
        $this->conditionVariableTranslatorCollection = $conditionVariableTranslatorCollection;
    }

    public function getConditionTranslatorCollection(): ConditionTranslatorCollection
    {
        return $this->conditionTranslatorCollection;
    }

    public function getConditionVariableTranslatorCollection(): ConditionVariableTranslatorCollection
    {
        return $this->conditionVariableTranslatorCollection;
    }
}
