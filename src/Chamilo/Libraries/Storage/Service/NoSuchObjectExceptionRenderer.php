<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NoSuchObjectExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return NoSuchObjectException::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans(
            'ObjectNotExist', [
            '%ObjectType%' => $userException->getObjectType(),
            '%ObjectIdentifier%' => $userException->getObjectIdentifier()
        ], StringUtilities::LIBRARIES
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('NoSuchObjectTitle', [], StringUtilities::LIBRARIES);
    }
}