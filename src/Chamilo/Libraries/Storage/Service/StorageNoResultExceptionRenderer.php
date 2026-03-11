<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageNoResultExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return StorageMethodException::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return 'No results for ' . $userException->getDataClassStorageUnitName() . ' [' . $userException->getQuery() .
            ']';
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('StorageNoResultExceptionTitle', [], StringUtilities::LIBRARIES);
    }
}