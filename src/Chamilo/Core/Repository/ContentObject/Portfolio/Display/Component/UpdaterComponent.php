<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to update the content of a portfolio item or folder
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends ItemComponent
{

    /**
     * Executes this component
     */
    public function build()
    {
        if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            $selected_complex_content_object_item = $this->get_current_complex_content_object_item();
            $content_object = $this->get_current_content_object();
            
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, 
                new PersonalWorkspace($this->get_user()), 
                $content_object, 
                'edit', 
                'post', 
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                        self::PARAM_STEP => $this->get_current_step())));
            
            if ($form->validate())
            {
                $succes = $form->update_content_object();
                
                if ($succes)
                {
                    Event::trigger(
                        'Activity', 
                        \Chamilo\Core\Repository\Manager::context(), 
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED, 
                            Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                            Activity::PROPERTY_DATE => time(), 
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->get_id(), 
                            Activity::PROPERTY_CONTENT => $content_object->get_title()));
                }
                
                if ($succes && $form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $content_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();
                    
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class_name(), 
                            ComplexContentObjectItem::PROPERTY_PARENT), 
                        new StaticConditionVariable($old_id), 
                        ComplexContentObjectItem::get_table_name());
                    $parameters = new DataClassRetrievesParameters($condition);
                    $children = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                        ComplexContentObjectItem::class_name(), 
                        $parameters);
                    
                    $failures = 0;
                    
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        
                        if (! $child->update())
                        {
                            $failures ++;
                        }
                    }
                    
                    $succes = ($succes) && ($failures == 0);
                }
                
                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'), 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES));
                
                $params = array();
                $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
                
                $this->redirect($message, (! $succes), $params);
            }
            else
            {
                if ($this->get_current_node()->is_root())
                {
                    $title = Translation::get('ChangeIntroduction');
                }
                else
                {
                    $title = Translation::get('EditContentObject');
                }
                
                $trail = BreadcrumbTrail::getInstance();
                $trail->add(
                    new Breadcrumb($this->get_url(array(self::PARAM_STEP => $this->get_current_step())), $title));
                
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP);
    }
}