<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\ComplexPortfolio;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to add content to the portfolio
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends ItemComponent implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * Executes this component
     */
    public function build()
    {
        if (! $this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $template = \Chamilo\Core\Repository\Configuration::registration_default_by_type(
            ClassnameUtilities::getInstance()->getNamespaceParent(Portfolio::context(), 2));

        $selected_template_id = TypeSelector::get_selection();

        if ($selected_template_id == $template->get_id())
        {
            $variable = 'AddFolder';
        }
        else
        {
            $variable = 'CreatorComponent';
        }
        BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url(), Translation::get($variable)));

        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $exclude = $this->detemine_excluded_content_object_ids($this->get_current_content_object()->get_id());

            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration);
            $component->set_excluded_objects($exclude);

            return $component->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects($this->getUser());
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

                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $object_id);

                if (! $object instanceof Portfolio)
                {
                    $new_object = ContentObject::factory(PortfolioItem::class_name());
                    $new_object->set_owner_id($this->get_user_id());
                    $new_object->set_title(PortfolioItem::get_type_name());
                    $new_object->set_description(PortfolioItem::get_type_name());
                    $new_object->set_parent_id(0);
                    $new_object->set_reference($object_id);
                    $new_object->create();
                }
                else
                {
                    $new_object = $object;
                }

                if ($new_object instanceof Portfolio)
                {
                    $wrapper = new ComplexPortfolio();
                }
                else
                {
                    $wrapper = new \Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\ComplexPortfolioItem();
                }

                $wrapper->set_ref($new_object->get_id());
                $wrapper->set_parent($this->get_current_content_object()->get_id());
                $wrapper->set_user_id($this->get_user_id());
                $wrapper->set_display_order(
                    \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order(
                        $this->get_current_content_object()->get_id()));

                if (! $wrapper->create())
                {
                    $failures ++;
                }
                else
                {
                    Event::trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $this->get_current_node()->get_content_object()->get_id(),
                            Activity::PROPERTY_CONTENT => $this->get_current_node()->get_content_object()->get_title() .
                                 ' > ' . $object->get_title()));
                }
            }

            if (count($object_ids) > 0 && ! $failures)
            {
                $current_parents_content_object_ids = $this->get_current_node()->get_parents_content_object_ids(
                    true,
                    true);

                if (count($object_ids) == 1)
                {
                    $current_parents_content_object_ids[] = $object_ids[0];
                }

                $this->get_root_content_object()->get_complex_content_object_path()->reset();
                $new_node = $this->get_root_content_object()->get_complex_content_object_path()->follow_path_by_content_object_ids(
                    $current_parents_content_object_ids);

                $next_step = $new_node->get_id();
            }
            else
            {
                $next_step = $this->get_current_step();
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
                Translation::get(
                    $message,
                    array('OBJECT' => Translation::get('Item'), 'OBJECTS' => Translation::get('Items')),
                    Utilities::COMMON_LIBRARIES),
                ($failures ? true : false),
                array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, self::PARAM_STEP => $next_step));
        }
    }

    /**
     * Determine which content objects can't be added to this portfolio
     *
     * @return int[]
     */
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
        return array(self::PARAM_STEP);
    }

    /**
     *
     * @see \core\repository\viewer\ViewerInterface::get_allowed_content_object_types()
     */
    public function get_allowed_content_object_types()
    {
        return $this->get_root_content_object()->get_allowed_types();
    }
}
