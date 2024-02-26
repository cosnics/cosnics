<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseDeleteException extends Exception
{
    protected Condition $condition;

    protected string $dataClassStorageUnitName;

    public function __construct(
        string $dataClassStorageUnitName, ?Condition $condition, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->condition = $condition;

        parent::__construct(
            'Delete for ' . $dataClassStorageUnitName . ' with condition ' . serialize($condition) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}