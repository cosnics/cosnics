<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table
 * @author  Tom Goethals - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RequestTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public const PROPERTY_AUTHOR = 'author';

    public const TABLE_IDENTIFIER = Manager::PARAM_REQUEST_IDS;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected DatetimeUtilities $datetimeUtilities;

    protected User $user;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->addAction(

                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_INDEX_VISIBILITY_CHANGER
                        ]
                    ), $translator->trans('ToggleIndexVisibility', [], Manager::CONTEXT)
                )
            );
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_AUTHOR, $this->getTranslator()->trans('Author', [], Manager::CONTEXT))
        );
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_REQUEST_TIME));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_PERCENTAGE));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_STATUS));
        $this->addColumn(
            new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_VISIBLE_IN_INDEX)
        );
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $object): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_DESCRIPTION :
                return htmlentities(
                    StringUtilities::getInstance()->truncate(
                        $object->getDefaultProperty(ContentObject::PROPERTY_DESCRIPTION), 50
                    )
                );
            case self::PROPERTY_AUTHOR :
                return $object->getOptionalProperty(User::PROPERTY_FIRSTNAME) . ' ' .
                    $object->getOptionalProperty(User::PROPERTY_LASTNAME);
            case Request::PROPERTY_REQUEST_TIME :
                return $this->getDatetimeUtilities()->formatLocaleDate(
                    null, (int) $object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME)
                );
            case Request::PROPERTY_STATUS :
                return Request::status_as_string($object->getOptionalProperty(Request::PROPERTY_STATUS));
            case Request::PROPERTY_PERCENTAGE :
                return $object->getOptionalProperty(Request::PROPERTY_PERCENTAGE) . '%';
            case Request::PROPERTY_VISIBLE_IN_INDEX :
                return $translator->trans(
                    $object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX) ? 'YesVisible' : 'NoVisible', [],
                    Manager::CONTEXT
                );
        }

        return parent::renderCell($column, $resultPosition, $object);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $object): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('ViewResult', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'),
                $urlGenerator->fromRequest(
                    [
                        ToolManager::PARAM_ACTION => Manager::ACTION_VIEW_RESULT,
                        Manager::PARAM_REQUEST_IDS => $object->get_id()
                    ]
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($object->getOptionalProperty(Request::PROPERTY_STATUS) != Request::STATUS_DUPLICATE)
        {
            if (!$object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX))
            {
                $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
                $translation = $translator->trans('AddDocumentToIndex', [], Manager::CONTEXT);
            }
            else
            {
                $glyph = new FontAwesomeGlyph('eye');
                $translation = $translator->trans('RemoveDocumentFromIndex', [], Manager::CONTEXT);
            }

            $toolbar->add_item(
                new ToolbarItem(
                    $translation, $glyph, $urlGenerator->fromRequest(
                    [
                        ToolManager::PARAM_ACTION => Manager::ACTION_INDEX_VISIBILITY_CHANGER,
                        Manager::PARAM_REQUEST_IDS => $object->get_id()
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
