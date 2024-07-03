<?php
namespace Chamilo\Libraries\Storage\Architecture\Exceptions;

use Exception;

/**
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageLastInsertedIdentifierException extends Exception
{

    protected string $dataClassStorageUnitName;

    public function __construct(string $dataClassStorageUnitName, string $exceptionMessage = '')
    {
        $this->dataClassStorageUnitName = $dataClassStorageUnitName;

        parent::__construct(
            'LastInsertedIdentifier for ' . $dataClassStorageUnitName . ' failed with the following message:' .
            $exceptionMessage
        );
    }
}