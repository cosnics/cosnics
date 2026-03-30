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
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected OnlineService $onlineService, protected OnlineTableRenderer $onlineTableRenderer,
        protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler,
        protected UserDetailsRenderer $userDetailsRenderer, protected UserService $userService
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getOnlineTableCondition(): ConditionInterface
    {
        $userIdentifiers = $this->onlineService->findDistinctOnlineUserIdentifiers();

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

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    private function renderOnlineTable(): string
    {
        $totalNumberOfItems = $this->userService->countUsers($this->getOnlineTableCondition());

        $tableParameterValues = $this->requestTableParameterValuesCompiler->determineParameterValues(
            $this->onlineTableRenderer->getParameterNames(), $this->onlineTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->userService->findUsers(
            $this->getOnlineTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $this->onlineTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $this->onlineTableRenderer->render($tableParameterValues, $users);
    }

    private function renderUserInformation(string $userIdentifier, User $user): string
    {
        return $this->userDetailsRenderer->renderUserDetailsForUserIdentifier($userIdentifier, $user);
    }
}
