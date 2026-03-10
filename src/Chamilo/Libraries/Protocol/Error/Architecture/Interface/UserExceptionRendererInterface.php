<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserExceptionRendererInterface
{
    /**
     * @return class-string<\Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface>
     */
    public function getUserExceptionClassName(): string;

    public function render(UserExceptionInterface $userException): string;

    public function renderMessage(UserExceptionInterface $userException): string;

    public function renderTitle(UserExceptionInterface $userException): string;
}