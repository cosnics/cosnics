<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorRegistry;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConditionVariableTranslator
{
    public function __construct(
        protected ConditionTranslatorRegistry $conditionTranslatorRegistry,
        protected ConditionVariableTranslatorRegistry $conditionVariableTranslatorRegistry
    )
    {
    }
}
