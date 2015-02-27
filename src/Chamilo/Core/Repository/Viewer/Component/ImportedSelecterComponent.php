<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Viewer\Table\Import\ImportTable;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class ImportedSelecterComponent extends Manager implements TableSupport, DelegateComponent
{

    public function run()
    {
        $table = new ImportTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $object_table_class_name
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        $content_object_ids = Session :: retrieve(self :: PARAM_IMPORTED_CONTENT_OBJECT_IDS);
        return new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $content_object_ids);
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @return \libraries\format\Toolbar
     */
    public function get_default_browser_actions($content_object)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($content_object->has_right(RepositoryRights :: USE_RIGHT, $this->get_user_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath() . 'action_publish.png',
                    $this->get_url(
                        array_merge(
                            $this->get_parameters(),
                            array(
                                self :: PARAM_ACTION => self :: ACTION_PUBLISHER,
                                self :: PARAM_ID => $content_object->get_id())),
                        false),
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($content_object->has_right(RepositoryRights :: VIEW_RIGHT, $this->get_user_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Preview'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                    $this->get_url(
                        array_merge(
                            $this->get_parameters(),
                            array(
                                self :: PARAM_ACTION => self :: ACTION_VIEWER,
                                self :: PARAM_ID => $content_object->get_id())),
                        false),
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($content_object->has_right(RepositoryRights :: COLLABORATE_RIGHT, $this->get_user_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditAndPublish'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_editpublish.png',
                    $this->get_url(
                        array_merge(
                            $this->get_parameters(),
                            array(
                                self :: PARAM_ACTION => self :: ACTION_CREATOR,
                                self :: PARAM_EDIT_ID => $content_object->get_id())),
                        false),
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($content_object instanceof ComplexContentObjectSupport)
        {

            $preview_url = \Chamilo\Core\Repository\Manager :: get_preview_content_object_url($content_object);
            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath() . 'action_preview.png',
                    $preview_url,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $onclick,
                    '_blank'));
        }

        return $toolbar;
    }
}
