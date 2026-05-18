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

    protected function renderCriteria(array $criteria): string
    {
        $identifierParts = [];

        foreach ($criteria as $identifierName => $identifierValue) {
            $identifierParts[] = $identifierName . ' = ' . $identifierValue;
        }

        return implode(', ', $identifierParts);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException $userException
     */
    public function renderMessage(UserExceptionInterface $userException): string
    {
        $parameters = ['%ObjectType%' => $userException->objectType];

        if ($userException->criteria) {
            $variable = 'NoSuchObjectWithCriteria';
            $parameters['%Criteria%'] = $this->renderCriteria($userException->criteria);
        }
        elseif ($userException->query) {
            $variable = 'NoSuchObjectWithQuery';
            $parameters['%Query%'] = $userException->query;
        }
        else {
            $variable = 'NoSuchObject';
        }

        return $this->translator->trans($variable, $parameters, StringUtilities::LIBRARIES);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException $userException
     */
    public function renderTitle(UserExceptionInterface $userException): string
    {
        return $this->translator->trans('NoSuchObjectTitle', [], StringUtilities::LIBRARIES);
    }
}