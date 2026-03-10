<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Service\AbstractUserExceptionRenderer;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderExceptionRenderer extends AbstractUserExceptionRenderer implements UserExceptionRendererInterface
{
    public function getUserExceptionClassName(): string
    {
        return DisplayOrderException::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans(
            'InvalidDisplayOrderExceptionMessage', [
            '%Type%' => $userException->getClassName(),
            '%Id%' => $userException->getIdentifier(),
            '%Context%' => $userException->getDisplayOrderContext(),
            '%DisplayOrder%' => $userException->getDisplayOrder(),
            '%Count%' => $userException->getNumberOfOtherDisplayOrdersInContext()
        ], StringUtilities::LIBRARIES
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException $userException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->getTranslator()->trans('StorageMethodExceptionTitle', [], StringUtilities::LIBRARIES);
    }
}