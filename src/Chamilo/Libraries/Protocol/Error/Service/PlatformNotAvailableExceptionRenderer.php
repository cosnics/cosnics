<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
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
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\PlatformNotAvailableException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('PlatformNotAvailableMessage', [], StringUtilities::LIBRARIES);
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\PlatformNotAvailableException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('PlatformNotAvailableTitle', [], StringUtilities::LIBRARIES);
    }
}