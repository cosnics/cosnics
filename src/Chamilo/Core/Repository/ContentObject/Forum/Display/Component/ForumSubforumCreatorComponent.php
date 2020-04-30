<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 *
 * @package repository.lib.complex_display.forum.component
 */
class ForumSubforumCreatorComponent extends Manager implements ViewerInterface,
    DelegateComponent
{

    public function run()
    {
        $forum = $this->getForum();

        if ($this->get_user()->is_platform_admin() || $this->get_user_id() == $forum->get_owner_id() ||
             $this->isForumManager($this->get_user()))
        {

            if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
            {

                $exclude = $this->retrieve_used_items($this->get_root_content_object()->get_id());

                $exclude[] = $this->get_root_content_object()->get_id();

                BreadcrumbTrail::getInstance()->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)),
                        $this->get_root_content_object()->get_title()));

                if ($this->get_complex_content_object_item())
                {

                    $forums_with_key_cloi = array();
                    $forums_with_key_cloi = $this->retrieve_children_from_root_to_cloi(
                        $this->get_root_content_object()->get_id(),
                        $this->get_complex_content_object_item()->get_id());

                    if ($forums_with_key_cloi)
                    {

                        foreach ($forums_with_key_cloi as $key => $value)
                        {

                            BreadcrumbTrail::getInstance()->add(
                                new Breadcrumb(
                                    $this->get_url(
                                        array(
                                            self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)),
                                    $value->get_title()));
                        }
                    }
                    else
                    {
                        throw new Exception('The forum you requested has not been found');
                    }
                }

                $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
                $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

                $component = $this->getApplicationFactory()->getApplication(
                    \Chamilo\Core\Repository\Viewer\Manager::context(),
                    $applicationConfiguration);
                $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
                $component->set_parameter(self::PARAM_ACTION, self::ACTION_CREATE_SUBFORUM);
                $component->set_parameter(
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                    $this->get_complex_content_object_item_id());
                $component->set_excluded_objects($exclude);

                return $component->run();
            }
            else
            {
                $cloi = ComplexContentObjectItem::factory(Forum::class);

                if ($this->get_complex_content_object_item())
                {
                    $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object_id());
                }

                $cloi->set_ref(\Chamilo\Core\Repository\Viewer\Manager::get_selected_objects());
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_display_order(
                    DataManager::select_next_display_order($cloi->get_parent()));

                $success = $cloi->create();

                $this->my_redirect($success);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    private function retrieve_used_items($object)
    {
        $items = array();
        $items = array_merge($items, $this->retrieve_used_items_parents($object));
        $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($object),
                ComplexContentObjectItem::get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {

            if ($complex_content_object_item->is_complex())
            {

                $items[] = $complex_content_object_item->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($complex_content_object_item->get_ref()));
            }
        }
        return $items;
    }

    private function retrieve_used_items_parents($object_id)
    {
        $items = array();
        $items[] = $object_id;
        $complex_content_object_items_parent = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_REF),
                new StaticConditionVariable($object_id)));
        while ($complex_content_object_item_parent = $complex_content_object_items_parent->next_result())
        {
            if ($complex_content_object_item_parent->is_complex())
            {

                $items = array_merge(
                    $items,
                    $this->retrieve_used_items_parents($complex_content_object_item_parent->get_parent()));
            }
        }
        return $items;
    }

    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectCreated' : 'ObjectNotCreated'),
                array('OBJECT' => Translation::get('Subforum')),
                Utilities::COMMON_LIBRARIES));

        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, ($success ? false : true), $params);
    }

    public function get_allowed_content_object_types()
    {
        return array(Forum::class);
    }
}
