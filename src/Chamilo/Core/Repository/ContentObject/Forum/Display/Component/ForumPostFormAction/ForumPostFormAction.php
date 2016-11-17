<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component\ForumPostFormAction;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Common functions for the create,edit and quote forms
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 */
abstract class ForumPostFormAction extends Manager implements DelegateComponent
{

    /**
     * add common breadcrumbtrails
     * 
     * @return breadcrumbtrails
     */
    public function add_common_breadcrumbtrails()
    {
        $trail = BreadcrumbTrail::getInstance();
        
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_FORUM, 
                        self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)), 
                $this->get_root_content_object()->get_title()));
        
        $complex_content_objects_path = $this->retrieve_children_from_root_to_cloi(
            $this->get_root_content_object()->get_id(), 
            $this->get_complex_content_object_item()->get_id());
        
        if ($complex_content_objects_path)
        {
            
            foreach ($complex_content_objects_path as $key => $value)
            {
                
                if ($value->get_type() == 'forum_topic')
                {
                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key, 
                                    self::PARAM_ACTION => self::ACTION_VIEW_TOPIC)), 
                            $value->get_title()));
                }
                else
                {
                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self::PARAM_ACTION => self::ACTION_VIEW_FORUM, 
                                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)), 
                            $value->get_title()));
                }
            }
        }
        else
        {
            throw new \Exception('The forum topic you requested has not been found in this forum');
        }
        
        return $trail;
    }
}
