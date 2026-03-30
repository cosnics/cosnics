<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotAllowedExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NotAllowedException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('NotAllowed', [], StringUtilities::LIBRARIES);
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('NotAllowedTitle', [], StringUtilities::LIBRARIES);
    }
}