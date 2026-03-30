<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return UserException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $userException->getMessage();
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('SomethingWentWrong', [], StringUtilities::LIBRARIES);
    }
}