<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Builder\Action\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CopierComponent extends Manager
{

    public function run()
    {
        $complexContentObjectItemIdentifiers = $this->getRequest()->get(
            \Chamilo\Core\Repository\Builder\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        
        if (! $complexContentObjectItemIdentifiers)
        {
            throw new \InvalidArgumentException();
        }
        elseif (! is_array($complexContentObjectItemIdentifiers))
        {
            $complexContentObjectItemIdentifiers = array($complexContentObjectItemIdentifiers);
        }
        
        $failedActions = 0;
        $copiedComplexContentObjectItemIdentifiers = array();
        
        foreach ($complexContentObjectItemIdentifiers as $complexContentObjectItemIdentifier)
        {
            $complexContentObjectItem = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(), 
                $complexContentObjectItemIdentifier);
            
            try
            {
                if (! $complexContentObjectItem instanceof ComplexContentObjectItem)
                {
                    throw new ObjectNotExistException(Translation::get('ComplexContentObjectItem'));
                }
                
                $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $complexContentObjectItem->get_ref());
                
                if (! $contentObject instanceof ContentObject)
                {
                    throw new ObjectNotExistException(Translation::get('ContentObject'));
                }
                
                $contentObjectCopier = new ContentObjectCopier(
                    $this->get_user(), 
                    array($contentObject->get_id()), 
                    new PersonalWorkspace($contentObject->get_owner()), 
                    $contentObject->get_owner_id(), 
                    new PersonalWorkspace($this->get_user()), 
                    $this->get_user_id());
                
                $copiedContentObjectIdentifiers = $contentObjectCopier->run();
                
                if ($contentObjectCopier->has_messages(ContentObjectCopier::TYPE_ERROR))
                {
                    throw new \Exception($contentObjectCopier->get_messages(ContentObjectCopier::TYPE_ERROR));
                }
                else
                {
                    $parentIdentifier = $this->get_parent_content_object_id();
                    $copiedContentObjectId = $copiedContentObjectIdentifiers[$contentObject->getId()];
                    
                    if (method_exists($this->get_parent(), 'get_helper_object'))
                    {
                        $helperObject = $this->get_parent()->get_helper_object($contentObject->get_type());
                        
                        if ($helperObject)
                        {
                            $helperObject->set_title($helperObject->get_type_name());
                            $helperObject->set_description($helperObject->get_type_name());
                            $helperObject->set_owner_id($this->getUser()->getId());
                            $helperObject->set_reference($copiedContentObjectId);
                            $helperObject->set_parent_id(0);
                            
                            if (! $helperObject->create())
                            {
                                throw new \Exception(Translation::get('HelperObjectCreationFailed'));
                            }
                            
                            $copiedContentObjectId = $helperObject->get_id();
                        }
                    }
                    
                    $copiedContentObjectType = \Chamilo\Core\Repository\Storage\DataManager::determineDataClassType(
                        ContentObject::class_name(), 
                        $copiedContentObjectId);
                    
                    $complexContentObjectItem = \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::factory(
                        $copiedContentObjectType);
                    $complexContentObjectItem->set_ref($copiedContentObjectId);
                    $complexContentObjectItem->set_parent($parentIdentifier);
                    $complexContentObjectItem->set_display_order(
                        \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order($parentIdentifier));
                    $complexContentObjectItem->set_user_id($this->getUser()->getId());
                    
                    if (! $complexContentObjectItem->create())
                    {
                        throw new \Exception(Translation::get('ComplexContentObjectItemCreationFailed'));
                    }
                    else
                    {
                        $copiedComplexContentObjectItemIdentifiers[] = $complexContentObjectItem->getId();
                    }
                }
            }
            catch (\Exception $exception)
            {
                $failedActions ++;
            }
        }
        
        if ($failedActions > 0)
        {
        }
        else
        {
            $numberOfCopiedContentOBjects = count($copiedComplexContentObjectItemIdentifiers);
            
            if ($numberOfCopiedContentOBjects > 1 || $numberOfCopiedContentOBjects == 0)
            {
                $this->redirect(
                    Translation::get(
                        'ObjectCopied', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES), 
                    false, 
                    array(
                        \Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager::ACTION_BROWSE, 
                        \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()->get_complex_content_object_item_id()));
            }
            else
            {
                $this->redirect(
                    Translation::get(
                        'ObjectCopied', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES), 
                    false, 
                    array(
                        \Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                        \Chamilo\Core\Repository\Builder\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => array_pop(
                            $copiedComplexContentObjectItemIdentifiers), 
                        \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()->get_complex_content_object_item_id()));
            }
        }
    }
}
