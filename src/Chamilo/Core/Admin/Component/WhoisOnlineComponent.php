<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\WhoIsOnlineService;
use Chamilo\Core\Admin\Table\WhoIsOnlineTableRenderer;
use Chamilo\Core\User\Service\UserDetails\UserDetailsRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package admin.lib.admin_manager.component
 */

/**
 * Component to view whois online
 */
class WhoisOnlineComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ViewWhoisOnline');

        if ($this->getUser() instanceof User)
        {
            $html = [];

            $html[] = $this->renderHeader();

            $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);

            if (isset($userIdentifier))
            {
                $html[] = $this->renderUserInformation($userIdentifier);
            }
            else
            {
                $html[] = $this->renderWhoIsOnlineTable();
            }

            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getUserDetailsRenderer(): UserDetailsRenderer
    {
        return $this->getService(UserDetailsRenderer::class);
    }

    public function getWhoIsOnlineService(): WhoIsOnlineService
    {
        return $this->getService(WhoIsOnlineService::class);
    }

    public function getWhoIsOnlineTableCondition()
    {
        $userIdentifiers = $this->getWhoIsOnlineService()->findDistinctOnlineUserIdentifiers();

        if (!empty($userIdentifiers))
        {
            return new InCondition(
                new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers
            );
        }
        else
        {
            return new EqualityCondition(
                new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), new StaticConditionVariable(- 1)
            );
        }
    }

    public function getWhoIsOnlineTableRenderer(): WhoIsOnlineTableRenderer
    {
        return $this->getService(WhoIsOnlineTableRenderer::class);
    }

    private function renderUserInformation(int $userIdentifier): string
    {
        return $this->getUserDetailsRenderer()->renderUserDetailsForUserIdentifier($userIdentifier, $this->getUser());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function renderWhoIsOnlineTable(): string
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getWhoIsOnlineTableCondition());
        $whoIsOnlineTableRenderer = $this->getWhoIsOnlineTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $whoIsOnlineTableRenderer->getParameterNames(), $whoIsOnlineTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getWhoIsOnlineTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $whoIsOnlineTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $whoIsOnlineTableRenderer->render($tableParameterValues, $users);
    }
}
