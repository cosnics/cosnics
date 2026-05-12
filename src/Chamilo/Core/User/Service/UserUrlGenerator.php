<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class UserUrlGenerator
{
    public function __construct(protected UrlGenerator $urlGenerator)
    {
    }

    public function getChangeUserUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::LOGIN_AS->value, $user);
    }

    public function getDeleteUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::DELETE->value, $user);
    }

    public function getDetailUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::VIEW->value, $user);
    }

    public function getUpdateUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::UPDATE->value, $user);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getUserActionUrl(string $action, User $user, array $additionalParameters = []): string
    {
        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => $action,
            Manager::PARAM_USER_ID => $user->getIdentifier()->toString()
        ];

        return $this->urlGenerator->fromParameters(array_merge($parameters, $additionalParameters));
    }
}