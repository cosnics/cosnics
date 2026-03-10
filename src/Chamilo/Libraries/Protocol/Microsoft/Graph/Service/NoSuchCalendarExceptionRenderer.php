<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchCalendarException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchCalendarExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NoSuchCalendarException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchCalendarException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return 'The system could not find a valid calendar for the given userIdentifier (' .
            $userException->getUserIdentifier() . ') and calendarIdentifier (' .
            $userException->getCalendarIdentifier() . ')';
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchCalendarException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('NoSuchCalendarExceptionTitle', [], StringUtilities::LIBRARIES);
    }
}