<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
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
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntryRequestTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const EPHORUS_TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus';

    public const PROPERTY_AUTHOR = 'author';

    public const TABLE_IDENTIFIER = Manager::PARAM_ENTRY_ID;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, StringUtilities $stringUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->stringUtilities = $stringUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [Manager::PARAM_ACTION => Manager::ACTION_CHANGE_INDEX_VISIBILITY]
                ), $translator->trans('ToggleIndexVisibility', [], self::EPHORUS_TRANSLATION_CONTEXT), false
            )
        );

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([Manager::PARAM_ACTION => Manager::ACTION_CREATE]),
                $translator->trans('AddDocuments', [], self::EPHORUS_TRANSLATION_CONTEXT), false
            )
        );

        return $actions;
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

        $this->addColumn(
            new DataClassPropertyTableColumn(
                Entry::class, Entry::PROPERTY_SUBMITTED
            )
        );
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_REQUEST_TIME));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_PERCENTAGE));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_STATUS));
        $this->addColumn(
            new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_VISIBLE_IN_INDEX)
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $object): string
    {
        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_DESCRIPTION :
                return htmlentities(
                    $this->getStringUtilities()->truncate(
                        $object->getDefaultProperty(ContentObject::PROPERTY_DESCRIPTION), 50
                    )
                );
            case self::PROPERTY_AUTHOR :
                return $object->getOptionalProperty(User::PROPERTY_FIRSTNAME) . ' ' .
                    $object->getOptionalProperty(User::PROPERTY_LASTNAME);
            case Request::PROPERTY_REQUEST_TIME :
                if ($object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME))
                {
                    return $datetimeUtilities->formatLocaleDate(
                        null, (int) $object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME)
                    );
                }

                return '-';
            case Entry::PROPERTY_SUBMITTED :
                return $datetimeUtilities->formatLocaleDate(
                    null, (int) $object->getOptionalProperty(Entry::PROPERTY_SUBMITTED)
                );
            case Request::PROPERTY_STATUS :
                if ($object->getOptionalProperty(Request::PROPERTY_STATUS) > 0)
                {
                    return Request::status_as_string($object->getOptionalProperty(Request::PROPERTY_STATUS));
                }
                else
                {
                    return '-';
                }
            case Request::PROPERTY_PERCENTAGE :
                if ($object->getOptionalProperty(Request::PROPERTY_STATUS) != null)
                {
                    return $object->getOptionalProperty(Request::PROPERTY_PERCENTAGE) . '%';
                }
                else
                {
                    return '-';
                }
            case Request::PROPERTY_VISIBLE_IN_INDEX :
                if ($object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX) != null)
                {
                    return $object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX) ? $translator->trans(
                        'YesVisible', [], self::EPHORUS_TRANSLATION_CONTEXT
                    ) : $translator->trans('NoVisible', [], self::EPHORUS_TRANSLATION_CONTEXT);
                }
                else
                {
                    return '-';
                }
        }

        return parent::renderCell($column, $resultPosition, $object);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $object): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $request_id = $object->getOptionalProperty(Request::PROPERTY_REQUEST_ID);
        if ($request_id != null)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewResult', [], self::EPHORUS_TRANSLATION_CONTEXT),
                    new FontAwesomeGlyph('chart-pie'), $urlGenerator->fromParameters(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_VIEW_RESULT,
                        Manager::PARAM_ENTRY_ID => $object->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME))
        {
            if ($object->getOptionalProperty(Request::PROPERTY_STATUS) != Request::STATUS_DUPLICATE)
            {
                if (!$object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX))
                {
                    $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
                    $translation = $translator->trans('AddDocumentToIndex', [], self::EPHORUS_TRANSLATION_CONTEXT);
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('eye');
                    $translation = $translator->trans('RemoveDocumentFromIndex', [], self::EPHORUS_TRANSLATION_CONTEXT);
                }

                $toolbar->add_item(
                    new ToolbarItem(
                        $translation, $glyph, $urlGenerator->fromParameters(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_CHANGE_INDEX_VISIBILITY,
                            Manager::PARAM_ENTRY_ID => $object->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('AddDocument', [], self::EPHORUS_TRANSLATION_CONTEXT),
                    new FontAwesomeGlyph('upload'), $urlGenerator->fromParameters(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                        Manager::PARAM_ENTRY_ID => $object->getId()
                    ]
                )
                )
            );
        }

        return $toolbar->render();
    }
}
