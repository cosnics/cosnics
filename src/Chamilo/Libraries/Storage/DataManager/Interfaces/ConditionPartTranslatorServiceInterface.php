<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConditionPartTranslatorServiceInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translate(
        DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart, bool $enableAliasing = true
    );
}