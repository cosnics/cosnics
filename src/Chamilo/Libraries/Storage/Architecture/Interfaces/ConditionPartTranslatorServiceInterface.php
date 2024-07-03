<?php
namespace Chamilo\Libraries\Storage\Architecture\Interfaces;

use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConditionPartTranslatorServiceInterface
{
    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string;
}