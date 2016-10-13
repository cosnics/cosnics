<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to add content to the page
 *
 * @package repository\content_object\page\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MergerComponent extends TabComponent implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * Executes this component
     */
    public function build()
    {
        if (! $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $template = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
            ClassnameUtilities :: getInstance()->getNamespaceParent(Page :: context(), 2));

        BreadcrumbTrail :: get_instance()->add(new Breadcrumb($this->get_url(), Translation :: get('CreatorComponent')));

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $exclude = array($this->get_current_content_object()->get_id());

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_excluded_objects($exclude);
            $component->set_actions(array(\Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER));
            return $component->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            $failures = 0;

            foreach ($object_ids as $object_id)
            {
                if ($this->get_current_node()->forms_cycle_with($object_id))
                {
                    $failures ++;
                    continue;
                }

                $page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $object_id);
                $complex_content_object_items = $page->get_questions(true);

                $excluded_items = $this->detemine_excluded_content_object_ids();

                foreach ($complex_content_object_items as $ccoi)
                {

                    if (count(array_diff(array($ccoi->get_ref()), $excluded_items)) == 1)
                    {

                        $object = $ccoi->get_ref_object();

                        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem :: factory(
                            $object->class_name());
                        $complex_content_object_item->set_ref($object->get_id());
                        $parent_id = $this->get_current_content_object()->get_id();
                        $complex_content_object_item->set_parent($parent_id);
                        $complex_content_object_item->set_display_order(
                            \Chamilo\Core\Repository\Storage\DataManager :: select_next_display_order($parent_id));
                        $complex_content_object_item->set_user_id($this->get_user_id());

                        if (! $complex_content_object_item->create())
                        {
                            $failures ++;
                        }
                        else
                        {
                            Event :: trigger(
                                'Activity',
                                \Chamilo\Core\Repository\Manager :: context(),
                                array(
                                    Activity :: PROPERTY_TYPE => Activity :: ACTIVITY_ADD_ITEM,
                                    Activity :: PROPERTY_USER_ID => $this->get_user_id(),
                                    Activity :: PROPERTY_DATE => time(),
                                    Activity :: PROPERTY_CONTENT_OBJECT_ID => $this->get_current_node()->get_content_object()->get_id(),
                                    Activity :: PROPERTY_CONTENT => $this->get_current_node()->get_content_object()->get_title() .
                                         ' > ' . $object->get_title()));
                        }
                    }
                }
            }

            if ($failures)
            {
                if (count($object_ids) == 1)
                {
                    $message = 'ObjectNotAdded';
                }
                else
                {
                    $message = 'ObjectsNotAdded';
                }
            }
            else
            {
                if (count($object_ids) == 1)
                {
                    $message = 'ObjectAdded';
                }
                else
                {
                    $message = 'ObjectsAdded';
                }
            }

            $this->redirect(
                Translation :: get(
                    $message,
                    array('OBJECT' => Translation :: get('Item'), 'OBJECTS' => Translation :: get('Items')),
                    Utilities :: COMMON_LIBRARIES),
                ($failures ? true : false),
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    self :: PARAM_STEP => $this->get_current_step()));
        }
    }

    private function detemine_excluded_content_object_ids()
    {
        $excluded_items = array();

        $current_node = $this->get_current_node();

        foreach ($current_node->get_children() as $child_node)
        {
            $excluded_items[] = $child_node->get_content_object()->get_id();
        }

        foreach ($current_node->get_parents(true) as $parent_node)
        {
            $excluded_items[] = $parent_node->get_content_object()->get_id();
        }

        return $excluded_items;
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }

    /**
     *
     * @see \core\repository\viewer\ViewerInterface::get_allowed_content_object_types()
     */
    public function get_allowed_content_object_types()
    {
        return array(Page :: class_name());
    }
}
