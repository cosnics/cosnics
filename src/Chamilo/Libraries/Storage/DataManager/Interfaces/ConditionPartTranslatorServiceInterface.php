<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

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
     * @return string
     */
    public function translateConditionPart(DataClassDatabaseInterface $dataClassDatabase, ConditionPart $conditionPart);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    public function translateCondition(DataClassDatabaseInterface $dataClassDatabase, Condition $condition);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return string
     */
    public function translateConditionVariable(DataClassDatabaseInterface $dataClassDatabase,
        ConditionVariable $conditionVariable);
}