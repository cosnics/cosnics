<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\Admin\UserInterface\Table\OnlineTableRenderer;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewOnlineComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);

        if (isset($userIdentifier)) {
            $html[] = $this->renderUserInformation($userIdentifier, $currentUser);
        }
        else {
            $html[] = $this->renderOnlineTable();
        }

        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getOnlineService(): OnlineService
    {
        return $this->getService(OnlineService::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getOnlineTableCondition(): ConditionInterface
    {
        $userIdentifiers = $this->getOnlineService()->findDistinctOnlineUserIdentifiers();

        if (!empty($userIdentifiers)) {
            return new InCondition(
                new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers
            );
        }
        else {
            return new EqualityCondition(
                new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), new StaticConditionVariable(- 1)
            );
        }
    }

    public function getOnlineTableRenderer(): OnlineTableRenderer
    {
        return $this->getService(OnlineTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getUserDetailsRenderer(): UserDetailsRenderer
    {
        return $this->getService(UserDetailsRenderer::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    private function renderOnlineTable(): string
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getOnlineTableCondition());
        $onlineTableRenderer = $this->getOnlineTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $onlineTableRenderer->getParameterNames(), $onlineTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getOnlineTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $onlineTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $onlineTableRenderer->render($tableParameterValues, $users);
    }

    private function renderUserInformation(string $userIdentifier, User $user): string
    {
        return $this->getUserDetailsRenderer()->renderUserDetailsForUserIdentifier($userIdentifier, $user);
    }
}
