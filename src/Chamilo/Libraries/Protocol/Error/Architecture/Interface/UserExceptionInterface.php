<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserExceptionInterface
{
    /**
     * @return class-string<\Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface>
     */
    public function getUserExceptionRendererClassName(): string;
}