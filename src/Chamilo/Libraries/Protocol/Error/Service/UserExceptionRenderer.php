<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
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
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $userException->getMessage();
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('SomethingWentWrong', [], StringUtilities::LIBRARIES);
    }
}