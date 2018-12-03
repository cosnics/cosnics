<?php

namespace Chamilo\Core\Queue\Exceptions;

/**
 * Indicates that the job has become irrelevant and should not be scheduled for retry
 *
 * @package Chamilo\Core\Queue\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobNoLongerValidException extends \Exception
{

}