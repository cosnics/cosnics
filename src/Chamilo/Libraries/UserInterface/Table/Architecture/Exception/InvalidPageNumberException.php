<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Exception;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\ExceptionInterface;
use Exception;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Exception
 * @author Pieterjan Broekaert <pieterjan.broekaert@hogent.be>
 */
class InvalidPageNumberException extends Exception implements ExceptionInterface
{
}