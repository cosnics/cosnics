<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchClassExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NoSuchClassException::class;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->translator->trans(
            'InvalidType', [
            '%ImplementationType%' => $userException->getImplementationType(),
            '%Type%' => $userException->getType()
        ], StringUtilities::LIBRARIES
        );
    }

    /**
     * @param \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('InvalidTypeTitle', [], StringUtilities::LIBRARIES);
    }
}