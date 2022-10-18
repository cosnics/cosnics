<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequest;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * CellRenderer for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class EntryRequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    const EPHORUS_TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus';

    /**
     * Returns the actions toolbar
     *
     * @param DataClass $object
     *
     * @return String
     */
    public function get_actions($object)
    {
        $toolbar = new Toolbar();

        $request_id = $object->getOptionalProperty(Request::PROPERTY_REQUEST_ID);
        if ($request_id != null)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewResult', null, self::EPHORUS_TRANSLATION_CONTEXT),
                    new FontAwesomeGlyph('chart-pie'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_VIEW_RESULT,
                        Manager::PARAM_ENTRY_ID => $object->getId(
                        )
                    )
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
                    $glyph = new FontAwesomeGlyph('eye', array('text-muted'));
                    $translation = Translation::get('AddDocumentToIndex', null, self::EPHORUS_TRANSLATION_CONTEXT);
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('eye');
                    $translation = Translation::get('RemoveDocumentFromIndex', null, self::EPHORUS_TRANSLATION_CONTEXT);
                }

                $toolbar->add_item(
                    new ToolbarItem(
                        $translation, $glyph, $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_CHANGE_INDEX_VISIBILITY,
                            Manager::PARAM_ENTRY_ID => $object->getId(
                            )
                        )
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('AddDocument', null, self::EPHORUS_TRANSLATION_CONTEXT),
                    new FontAwesomeGlyph('upload'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                        Manager::PARAM_ENTRY_ID => $object->getId(
                        )
                    )
                )
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * Renders the cell for a given column and row (object)
     *
     * @param NewObjectTableColumn $column
     * @param DataClass $object
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $object): string
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_DESCRIPTION :
                return htmlentities(
                    StringUtilities::getInstance()->truncate(
                        $object->getDefaultProperty(ContentObject::PROPERTY_DESCRIPTION), 50
                    )
                );
            case EntryRequestTableColumnModel::COLUMN_NAME_AUTHOR :
                return $object->getOptionalProperty(User::PROPERTY_FIRSTNAME) . ' ' .
                    $object->getOptionalProperty(User::PROPERTY_LASTNAME);
            // return $object->get_author()->get_fullname();
            case Request::PROPERTY_REQUEST_TIME :
                if ($object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME))
                {
                    return DatetimeUtilities::getInstance()->formatLocaleDate(
                        null, $object->getOptionalProperty(Request::PROPERTY_REQUEST_TIME)
                    );
                }

                return '-';
            case Entry::PROPERTY_SUBMITTED :
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    null, $object->getOptionalProperty(Entry::PROPERTY_SUBMITTED)
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
                    return $object->getOptionalProperty(Request::PROPERTY_VISIBLE_IN_INDEX) ? Translation::get(
                        'YesVisible', null, self::EPHORUS_TRANSLATION_CONTEXT
                    ) : Translation::get('NoVisible', null, self::EPHORUS_TRANSLATION_CONTEXT);
                }
                else
                {
                    return '-';
                }
        }

        return parent::renderCell($column, $object);
    }
}
