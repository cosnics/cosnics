<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
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
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans(
            'InvalidType', [
            '%ImplementationType%' => $userException->getImplementationType(),
            '%Type%' => $userException->getType()
        ], StringUtilities::LIBRARIES
        );
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('InvalidTypeTitle', [], StringUtilities::LIBRARIES);
    }
}