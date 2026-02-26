<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Table\UserTableRenderer;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Form\ButtonSearchForm;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar()) . '<br />';
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getAdminUserTableRenderer(): UserTableRenderer
    {
        return $this->getService(UserTableRenderer::class);
    }

    public function getButtonToolBar(): ButtonToolBar
    {
        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => self::CONTEXT, self::PARAM_ACTION => self::ACTION_BROWSE]
            )
        );

        $commonActions = new ButtonGroup();
        $translator = $this->getTranslator();

        if ($this->getUser()->isPlatformAdministrator()) {
            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->getUrlGenerator()->fromParameters(
                        [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_CREATE]
                    ), DisplayTypeEnum::ICON_AND_LABEL
                )
            );
        }

        $buttonToolBar->addButton($commonActions);

        return $buttonToolBar;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getUserTableCondition(): ?ConditionInterface
    {
        $searchProperties = [];
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $this->getButtonToolBarRenderer()->getConditions($searchProperties);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    protected function renderTable(): string
    {
        $this->getRequest()->query->set(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->getButtonToolBarRenderer()->getSearchForm()->getQuery()
        );

        $totalNumberOfItems = $this->getUserService()->countUsers($this->getUserTableCondition());
        $adminUserTableRenderer = $this->getAdminUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $adminUserTableRenderer->getParameterNames(), $adminUserTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getUserTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $adminUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $adminUserTableRenderer->render($tableParameterValues, $users);
    }
}
