<?php
namespace Chamilo\Libraries\Storage\Architecture\Exception;

use Chamilo\Libraries\Architecture\Exception\UserException;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageLastInsertedIdentifierException extends UserException
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