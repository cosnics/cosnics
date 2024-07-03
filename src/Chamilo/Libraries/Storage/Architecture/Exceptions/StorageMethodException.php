<?php
namespace Chamilo\Libraries\Storage\Architecture\Exceptions;

use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception\Database
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageMethodException extends Exception
{
    protected string $dataClassStorageUnitName;

    protected string $method;

    protected ?string $query;

    public function __construct(
        string $method, string $dataClassStorageUnitName, string $exceptionMessage = '', ?string $query = null
    )
    {
        $this->method = $method;
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;
        $this->query = $query;

        $message = $method . ' for ' . $dataClassStorageUnitName;

        if ($query)
        {
            $message .= '[' . $query . ']';
        }

        $message .= ' failed with the following message:' . $exceptionMessage;

        parent::__construct($message);
    }
}