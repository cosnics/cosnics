<?php
namespace Chamilo\Core\Repository\Display\Action\Component;

use Chamilo\Core\Repository\Display\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Original author unknown
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed_to_delete_child())
        {
            /*
             * if (Request::get('selected_cloi')) { $cloi_ids = Request::get('selected_cloi'); } else { $cloi_ids =
             * $_POST['selected_cloi']; }
             */
            
            $complex_content_object_item_ids = $this->getRequest()->get(
                \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
            
            if (! is_array($complex_content_object_item_ids))
            {
                $complex_content_object_item_ids = array($complex_content_object_item_ids);
            }
            
            $failures = 0;
            foreach ($complex_content_object_item_ids as $complex_content_object_item_id)
            {
                $complex_content_object_item = DataManager::retrieve_by_id(
                    ComplexContentObjectItem::class,
                    $complex_content_object_item_id);
                if (! $complex_content_object_item->delete())
                {
                    $failures ++;
                }
            }
            
            $succes = ($failures == 0);
            
            if (count($complex_content_object_item_ids) > 1)
            {
                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectsDeleted' : 'ObjectsNotDeleted'), 
                        array('OBJECTS' => Translation::get('ComplexContentObjectItems')), 
                        StringUtilities::LIBRARIES));
            }
            else
            {
                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectDeleted' : 'ObjectNotDeleted'), 
                        array('OBJECT' => Translation::get('ComplexContentObjectItem')), 
                        StringUtilities::LIBRARIES));
            }
            
            $this->redirectWithMessage(
                $message, 
                (! $succes), 
                array(
                    \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                    \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()), 
                array(\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
