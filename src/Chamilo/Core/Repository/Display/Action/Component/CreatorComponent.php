<?php
namespace Chamilo\Core\Repository\Display\Action\Component;

use Chamilo\Core\Repository\Display\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends Manager implements ViewerInterface
{

    public function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed_to_add_child())
        {
            $complex_content_object_item_id = $this->get_complex_content_object_item_id();

            if (!$this->get_root_content_object())
            {
                return $this->display_error_page(Translation::get('NoParentSelected'));
            }

            $type = $this->getRequest()->query->get('type');

            if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
            {
                $component = $this->getApplicationFactory()->getApplication(
                    \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
                );
                $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
                $component->set_parameter(
                    \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION,
                    \Chamilo\Core\Repository\Display\Manager::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM
                );
                $component->set_parameter('cid', $complex_content_object_item_id);
                $component->set_parameter('type', $type);

                return $component->run();
            }
            else
            {
                $cloi = ComplexContentObjectItem::factory($type);

                $cloi->set_ref(\Chamilo\Core\Repository\Viewer\Manager::get_selected_objects());
                $cloi->set_user_id($this->get_user_id());

                if ($complex_content_object_item_id)
                {
                    $complex_content_object_item = DataManager::retrieve_by_id(
                        ComplexContentObjectItem::class, $complex_content_object_item_id
                    );
                    $cloi->set_parent($complex_content_object_item->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object()->get_id());
                }

                $cloi->set_display_order(
                    DataManager::select_next_display_order($cloi->get_parent())
                );

                $succes = $cloi->create();
                $this->my_redirect($complex_content_object_item_id, $succes);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    public function get_allowed_content_object_types()
    {
        return [$this->getRequest()->query->get('type')];
    }

    private function my_redirect($complex_content_object_item_id, $succes)
    {
        $message = htmlentities(
            Translation::get(
                ($succes ? 'ObjectCreated' : 'ObjectNotCreated'), ['OBJECT' => Translation::get('ContentObject')],
                StringUtilities::LIBRARIES
            )
        );

        $params = [];
        $params['cid'] = $complex_content_object_item_id;
        $params[\Chamilo\Core\Repository\Display\Manager::PARAM_ACTION] =
            \Chamilo\Core\Repository\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirectWithMessage($message, (!$succes), $params);
    }
}
