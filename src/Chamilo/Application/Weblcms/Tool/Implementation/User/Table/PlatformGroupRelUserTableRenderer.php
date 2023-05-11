<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PlatformGroupRelUserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    protected Application $application;

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupRelUser
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $groupRelUser): string
    {
        if ($column->get_name() == GroupRelUser::PROPERTY_USER_ID)
        {
            $userIdentifier = parent::renderCell($column, $resultPosition, $groupRelUser);
            $user = DataManager::retrieve_by_id(User::class, (int) $userIdentifier);

            return $user->get_fullname();
        }

        return parent::renderCell($column, $resultPosition, $groupRelUser);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupRelUser
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $groupRelUser): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        // always show details
        $parameters = [];
        $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_USER_DETAILS;
        $parameters[Manager::PARAM_OBJECTS] = $groupRelUser->get_user_id();
        $details_url = $urlGenerator->fromRequest($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Details', [], Manager::CONTEXT), new FontAwesomeGlyph('info-circle'), $details_url,
                ToolbarItem::DISPLAY_ICON
            )
        );

        // if we have editing rights, display the reporting action but never
        // allow unsubscribe
        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('UnsubscribeNotAvailableForGroups', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('minus-square', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );

            $params = [];
            $params[Manager::PARAM_OBJECTS] = $groupRelUser->get_user_id();
            $params[ToolManager::PARAM_ACTION] = Manager::ACTION_REPORTING;
            $reporting_url = $urlGenerator->fromRequest($params);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Report', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'),
                    $reporting_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
