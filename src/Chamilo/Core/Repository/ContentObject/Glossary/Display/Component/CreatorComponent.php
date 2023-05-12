<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Description of glossary_item_creator
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CreatorComponent extends Manager implements ViewerInterface, DelegateComponent
{

    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_add_child())
        {
            throw new NotAllowedException();
        }

        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)),
                    $this->get_root_content_object()->get_title()));

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component->set_parameter(self::PARAM_ACTION, self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM);
            $component->set_parameter(
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $this->get_complex_content_object_item_id());
            return $component->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            $failures = 0;
            foreach ($object_ids as $object_id)
            {
                $ccoi = ComplexContentObjectItem::factory(GlossaryItem::class);
                $ccoi->set_parent($this->get_root_content_object_id());
                $ccoi->set_ref($object_id);
                $ccoi->set_user_id($this->get_user_id());
                $ccoi->set_display_order(
                    DataManager::select_next_display_order($ccoi->get_parent()));
                if (! $ccoi->create())
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
                array('OBJECT' => Translation::get('GlossaryItem')),
                StringUtilities::LIBRARIES));

        $parameters = [];
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
        $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirectWithMessage($message, ! $success, $parameters);
    }

    public function get_allowed_content_object_types()
    {
        return array(GlossaryItem::class);
    }
}
