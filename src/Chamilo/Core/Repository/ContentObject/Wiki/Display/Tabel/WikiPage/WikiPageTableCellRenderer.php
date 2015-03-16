<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Tabel\WikiPage;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class WikiPageTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $publication)
    {
        $this->publication_id = Request :: get('publication_id');

        $wiki_page = $this->get_publication_from_complex_content_object_item($publication);
        $this->complex_id = $publication->get_id();

        if ($publication->get_additional_property('is_homepage') == 1)
        {
            $homepage = ' (' . Translation :: get('Homepage') . ')';
        }

        if (isset($wiki_page))
        {
            if ($property = $column->get_name())
            {
                switch ($property)
                {
                    case ContentObject :: PROPERTY_TITLE :
                        return '<a href="' .
                             $this->get_component()->get_url(
                                array(
                                    Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_WIKI_PAGE,
                                    Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)) . '">' .
                             htmlspecialchars($wiki_page->get_title()) . '</a>' . $homepage;
                    case Translation :: get('Versions') :
                        return $wiki_page->get_version_count();
                    case ContentObject :: PROPERTY_DESCRIPTION :
                        $description = str_ireplace(
                            ']]',
                            '',
                            str_ireplace('[[', '', str_ireplace('=', '', $wiki_page->get_description())));
                        return Utilities :: truncate_string($description, 50);
                }
            }

            return parent :: render_cell($column, $wiki_page);
        }
    }

    public function get_actions($publication)
    {
        $toolbar = new Toolbar();
        if ($this->get_component()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $publication->get_id())),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        if ($this->get_component()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $publication->get_id())),
                    ToolbarItem :: DISPLAY_ICON));

            if (($publication->get_additional_property('is_homepage') == 0))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('SetAsHomepage'),
                        Theme :: getInstance()->getCommonImagePath('Action/Home'),
                        $this->get_component()->get_url(
                            array(
                                Manager :: PARAM_ACTION => Manager :: ACTION_SET_AS_HOMEPAGE,
                                Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)),
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('SetAsHomepage'),
                        Theme :: getInstance()->getCommonImagePath('Action/HomeNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }

    /*
     * private function get_publish_links($wiki_page) { }
     */
    private function get_publication_from_complex_content_object_item($clo_item)
    {
        $publication = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($clo_item->get_ref());
        return $publication;
    }
}
