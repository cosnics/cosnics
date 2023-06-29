<?php
namespace Chamilo\Application\Weblcms\Request\Table;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Quota\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagementRequestTableRenderer extends RequestTableRenderer
{
    public const PROPERTY_USER = 'User';

    public function getTableActions(): TableActions
    {
        $tableActions = parent::getTableActions();
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        if ($this->getUser()->isPlatformAdmin())
        {
            $tableActions->addAction(
                new TableAction(
                    $urlGenerator->fromParameters(
                        [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_GRANT]
                    ), $translator->trans('GrantSelected', [], StringUtilities::LIBRARIES)
                )
            );

            $tableActions->addAction(
                new TableAction(
                    $urlGenerator->fromParameters(
                        [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DENY]
                    ), $translator->trans('DenySelected', [], StringUtilities::LIBRARIES)
                )
            );
        }

        return $tableActions;
    }

    protected function initializeColumns(): void
    {
        parent::initializeColumns();

        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_USER, $translator->trans(self::PROPERTY_USER, [], Manager::CONTEXT))
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $request): string
    {
        if ($column->get_name() == self::PROPERTY_USER)
        {
            return $request->get_user()->get_fullname();
        }

        return parent::renderCell($column, $resultPosition, $request);
    }

}