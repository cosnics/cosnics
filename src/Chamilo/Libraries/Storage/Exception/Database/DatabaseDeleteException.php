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

    protected string $dataClassName;

    public function __construct(
        string $dataClassName, ?Condition $condition, string $exceptionMessage = ''
    )
    {
        $this->dataClassName = $dataClassName;
        $this->condition = $condition;

        parent::__construct(
            'Delete for ' . $dataClassName . ' with condition ' . serialize($condition) .
            ', failed with the following message:' . $exceptionMessage
        );
    }
}