<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchParameterExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NoSuchParameterException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->translator->trans(
            'MissingParameter', [
            '%Parameter%' => $userException->getParameter()
        ], StringUtilities::LIBRARIES
        );
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('MissingParameterTitle', [], StringUtilities::LIBRARIES);
    }
}