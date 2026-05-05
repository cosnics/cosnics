<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\AbstractUserExceptionRenderer;
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
        return $this->translator->trans(
            'NoSuchObject', [
            '%ObjectType%' => $userException->getObjectType(),
            '%ObjectIdentifiers%' => $this->renderObjectIdentifiers($userException->getObjectIdentifiers())
        ], StringUtilities::LIBRARIES
        );
    }

    protected function renderObjectIdentifiers(array $objectIdentifiers): string
    {
        $identifierParts = [];

        foreach ($objectIdentifiers as $identifierName => $identifierValue) {
            $identifierParts[] = $identifierName . ' = ' . $identifierValue;
        }

        return implode(', ', $identifierParts);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('NoSuchObjectTitle', [], StringUtilities::LIBRARIES);
    }
}