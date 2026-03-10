<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageMethodExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return StorageMethodException::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        $message = $userException->getMethod() . ' for ' . $userException->getDataClassStorageUnitName();

        if ($userException->getQuery()) {
            $message .= '[' . $userException->getQuery() . ']';
        }

        $message .= ' failed';

        if ($userException->getExceptionMessage()) {
            $message .= ' with message: ' . $userException->getExceptionMessage();
        }

        return $message;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('StorageMethodExceptionTitle', [], StringUtilities::LIBRARIES);
    }
}