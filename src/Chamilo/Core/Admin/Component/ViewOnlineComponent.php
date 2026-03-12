<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\Admin\UserInterface\Table\OnlineTableRenderer;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewOnlineComponent extends Manager
{
    protected OnlineService $onlineService;

    protected OnlineTableRenderer $onlineTableRenderer;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    protected UserDetailsRenderer $userDetailsRenderer;

    protected UserService $userService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UserService $userService,
        UrlGenerator $urlGenerator, OnlineService $onlineService, OnlineTableRenderer $onlineTableRenderer,
        UserDetailsRenderer $userDetailsRenderer,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);

        $this->userService = $userService;
        $this->onlineService = $onlineService;
        $this->onlineTableRenderer = $onlineTableRenderer;
        $this->userDetailsRenderer = $userDetailsRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
    }

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
        return $this->onlineService;
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
        return $this->onlineTableRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->requestTableParameterValuesCompiler;
    }

    public function getUserDetailsRenderer(): UserDetailsRenderer
    {
        return $this->userDetailsRenderer;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
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
