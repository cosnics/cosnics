<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotAuthenticatedExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NotAuthenticatedException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        $translator = $this->getTranslator();

        if (!$userException->getMessage()) {
            return $translator->trans('NotAuthenticated', [], StringUtilities::LIBRARIES);
        }

        return $userException->getMessage();
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('NotAuthenticatedTitle', [], StringUtilities::LIBRARIES);
    }
}