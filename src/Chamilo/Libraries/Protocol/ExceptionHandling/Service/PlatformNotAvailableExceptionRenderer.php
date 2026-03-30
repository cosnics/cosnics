<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PlatformNotAvailableExceptionRenderer extends AbstractUserExceptionRenderer
    implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return PlatformNotAvailableException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\PlatformNotAvailableException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('PlatformNotAvailableMessage', [], StringUtilities::LIBRARIES);
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\PlatformNotAvailableException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('PlatformNotAvailableTitle', [], StringUtilities::LIBRARIES);
    }
}