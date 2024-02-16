<?php
namespace Chamilo\Libraries\Storage\Exception\Database;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatabaseUpdateException extends Exception
{

    protected Condition $condition;

    protected string $dataClassStorageUnitName;

    protected array $propertiesToUpdate;

    public function __construct(
        string $dataClassStorageUnitName, Condition $condition, array $propertiesToUpdate, string $exceptionMessage = ''
    )
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->condition = $condition;
        $this->propertiesToUpdate = $propertiesToUpdate;

        parent::__construct(
            'Update for ' . $dataClassStorageUnitName . ' with condition ' . serialize($condition) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}