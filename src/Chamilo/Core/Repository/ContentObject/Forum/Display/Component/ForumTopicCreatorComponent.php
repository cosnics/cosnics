<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_display.forum.component
 */
class ForumTopicCreatorComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface,
    DelegateComponent
{

    public function run()
    {
        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)),
                    $this->get_root_content_object()->get_title()));

            if ($this->get_complex_content_object_item())
            {

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
                    throw new \Exception('The forum you requested has not been found');
                }
            }

            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration);
            $component->set_parameter(self::PARAM_ACTION, self::ACTION_CREATE_TOPIC);
            $component->set_parameter(
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $this->get_complex_content_object_item_id());

            return $component->run();
        }
        else
        {
            $object_id = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();

            if (! is_array($object_id))
            {
                $object_id = array($object_id);
            }

            $failures = 0;
            foreach ($object_id as $key => $value)
            {
                $cloi = ComplexContentObjectItem::factory(ForumTopic::class_name());

                if ($this->get_complex_content_object_item())
                {
                    $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object_id());
                }

                $cloi->set_ref($value);
                $cloi->set_user_id($this->get_user_id());

                $cloi->set_display_order(
                    \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order(
                        $cloi->get_parent(),
                        ForumTopic::class_name()));

                if (! $cloi->create())
                {
                    $failures ++;
                }
            }

            $this->my_redirect($failures == 0);
        }
    }

    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectCreated' : 'ObjectNotCreated'),
                array('OBJECT' => Translation::get('ForumTopic')),
                Utilities::COMMON_LIBRARIES));

        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, ($success ? false : true), $params);
    }

    public function get_allowed_content_object_types()
    {
        return array(ForumTopic::class_name());
    }
}
