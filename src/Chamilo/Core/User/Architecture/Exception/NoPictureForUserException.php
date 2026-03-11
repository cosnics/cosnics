<?php
namespace Chamilo\Core\User\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\ExceptionInterface;
use Exception;

/**
 * @package Chamilo\Core\User\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoPictureForUserException extends Exception implements ExceptionInterface
{
}