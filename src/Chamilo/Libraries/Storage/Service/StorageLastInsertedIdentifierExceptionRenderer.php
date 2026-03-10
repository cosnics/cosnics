<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageLastInsertedIdentifierExceptionRenderer extends AbstractUserExceptionRenderer
    implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return StorageLastInsertedIdentifierException::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        $message = 'LastInsertedIdentifier for ' . $userException->getDataClassStorageUnitName() . ' failed';

        if ($userException->getExceptionMessage()) {
            $message .= ' with message: ' . $userException->getExceptionMessage();
        }

        return $message;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('StorageLastInsertedIdentifierExceptionTitle', [],
            StringUtilities::LIBRARIES);
    }
}