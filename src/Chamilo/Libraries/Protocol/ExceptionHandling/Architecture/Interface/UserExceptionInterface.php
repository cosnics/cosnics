<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserExceptionInterface extends ExceptionInterface
{
    /**
     * @return class-string<\Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface>
     */
    public function getUserExceptionRendererClassName(): string;
}