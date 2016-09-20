<?php
namespace Chamilo\Core\Repository\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'display_action';
    const PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'cloi';
    const PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'selected_cloi';
    const PARAM_DIRECTION = 'direction';
    const PARAM_TYPE = 'type';
    const PARAM_ATTACHMENT_ID = 'attachment_id';
    const ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM = 'Deleter';
    const ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM = 'Updater';
    const ACTION_UPDATE_CONTENT_OBJECT = 'ContentObjectUpdater';
    const ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM = 'Creator';
    const ACTION_MERGE = 'Merger';
    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    const ACTION_VIEW_COMPLEX_CONTENT_OBJECT = 'Viewer';
    const DEFAULT_ACTION = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    protected $menu;

    /**
     * The current item in treemenu to determine where we are in the structure
     *
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     * The item we select to execute an action like update / delete / move etc
     *
     * @var ComplexContentObjectItem
     */
    private $selected_complex_content_object_item;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param unknown $user
     * @param unknown $parent
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent :: __construct($applicationConfiguration);

        $action = Request :: get(self :: PARAM_ACTION);
        $this->set_action($action);

        $this->set_parameter(self :: PARAM_TYPE, Request :: get(self :: PARAM_TYPE));

        $complex_content_object_item_id = Request :: get(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $this->set_parameter(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
            $this->complex_content_object_item = $this->get_complex_content_object_by_id(
                $complex_content_object_item_id);
        }

        $selected_complex_content_object_item_id = Request :: get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($selected_complex_content_object_item_id && ! is_array($selected_complex_content_object_item_id))
        {
            $this->set_parameter(
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $selected_complex_content_object_item_id);

            if (! is_array($selected_complex_content_object_item_id))
            {
                $this->selected_complex_content_object_item = $this->get_complex_content_object_by_id(
                    $selected_complex_content_object_item_id);
            }
            else
            {
                $this->selected_complex_content_object_item = array();

                foreach ($selected_complex_content_object_item_id as $id)
                {
                    $this->selected_complex_content_object_item[] = $this->get_complex_content_object_by_id($id);
                }
            }
        }
    }

    public static function factory($type, $application)
    {
        $class_name = 'Chamilo\Core\Repository\ContentObject\\' .
             StringUtilities :: getInstance()->createString($type)->upperCamelize() . '\Display\Manager';
        return new $class_name($application);
    }

    /**
     * Retrieves and validates a complex content object with a given id
     *
     * @param $complex_content_object_item_id int
     *
     * @return ComplexContentObjectItem
     */
    protected function get_complex_content_object_by_id($complex_content_object_item_id)
    {
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ComplexContentObjectItem :: class_name(),
            $complex_content_object_item_id);
        if (is_null($complex_content_object_item))
        {
            throw new ObjectNotExistException(
                Translation :: get('ComplexContentObjectItem'),
                $complex_content_object_item_id);
        }

        return $complex_content_object_item;
    }

    public function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    public function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    public function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    /**
     *
     * @param $complex_content_object_item ComplexContentObjectItem
     */
    public function set_complex_content_object_item(ComplexContentObjectItem $complex_content_object_item)
    {
        $this->complex_content_object_item = $complex_content_object_item;
    }

    public function get_selected_complex_content_object_item()
    {
        return $this->selected_complex_content_object_item;
    }

    public function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object()->get_id();
    }

    public function get_complex_content_object_item_id()
    {
        if ($this->complex_content_object_item)
        {
            return $this->complex_content_object_item->get_id();
        }
    }

    public function get_selected_complex_content_object_item_id()
    {
        if ($this->selected_complex_content_object_item)
        {
            return $this->selected_complex_content_object_item->get_id();
        }
    }

    // Common Code
    public function get_complex_content_object_menu()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->render_as_tree();
    }

    public function get_complex_content_object_breadcrumbs()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->get_breadcrumbs();
    }

    protected function build_complex_content_object_menu()
    {
        $this->menu = new \Chamilo\Core\Repository\Builder\Menu(
            $this->get_root_content_object(),
            $this->get_complex_content_object_item(),
            $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT)));
    }

    // url building
    public function get_complex_content_object_item_update_url($complex_content_object_item)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    public function get_complex_content_object_item_delete_url($complex_content_object_item)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    /**
     * Builds the attachment url
     *
     * @param $attachment ContentObject
     * @param $selected_complex_content_object_item_id int [OPTIONAL] default null
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment,
        $selected_complex_content_object_item_id = null)
    {
        if (is_null($selected_complex_content_object_item_id))
        {
            $selected_complex_content_object_item_id = $this->get_selected_complex_content_object_item_id();
        }

        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_ATTACHMENT,
                self :: PARAM_ATTACHMENT_ID => $attachment->get_id(),
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item_id));
    }

    /**
     * Checks if a complex content object path node can be editted
     *
     * @param ComplexContentObjectPathNode $complexContentObjectPathNode
     *
     * @return bool
     */
    public function canEditComplexContentObjectPathNode(
        ComplexContentObjectPathNode $complexContentObjectPathNode = null
    )
    {
        if($this->get_application()->is_allowed_to_edit_content_object($complexContentObjectPathNode))
        {
            return true;
        }

        return $complexContentObjectPathNode->get_content_object()->get_owner_id() == $this->getUser()->getId();
    }
}
