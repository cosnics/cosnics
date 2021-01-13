<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Exception;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Exception
 */
class UnknownAzureUserIdException extends \Exception
{
    /**
     * UnknownAzureIdException constructor.
     * @param string $azureId
     */
    public function __construct(string $azureId)
    {
        parent::__construct(
            'Unknown Azure User Id: ' . $azureId);
    }
}
