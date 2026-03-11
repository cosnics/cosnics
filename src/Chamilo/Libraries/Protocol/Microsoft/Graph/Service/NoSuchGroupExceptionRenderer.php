<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchGroupExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NoSuchGroupException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return 'Group does not exist in Microsoft 365: ' . $userException->getGroupIdentifier();
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('NoSuchGroupExceptionTitle', [], StringUtilities::LIBRARIES);
    }
}