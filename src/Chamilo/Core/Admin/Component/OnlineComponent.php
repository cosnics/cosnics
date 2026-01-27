<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\Admin\UserInterface\Table\OnlineTableRenderer;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OnlineComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);

        if (isset($userIdentifier))
        {
            $html[] = $this->renderUserInformation($userIdentifier);
        }
        else
        {
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
    public function getOnlineTableCondition(): Condition
    {
        $userIdentifiers = $this->getOnlineService()->findDistinctOnlineUserIdentifiers();

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
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

    private function renderUserInformation(string $userIdentifier): string
    {
        return $this->getUserDetailsRenderer()->renderUserDetailsForUserIdentifier($userIdentifier, $this->getUser());
    }
}
