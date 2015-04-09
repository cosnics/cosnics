<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class VisibilityChangerComponent extends Manager
{
    const MESSAGE_VISIBILITY_CHANGED = 'VisibilityChanged';
    const MESSAGE_VISIBILITY_NOT_CHANGED = 'VisibilityNotChanged';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $complex_item_ids = Request :: get(self :: PARAM_COMPLEX_QUESTION_ITEM_ID);
        
        if (! empty($complex_item_ids))
        {
            if (! is_array($complex_item_ids))
            {
                $complex_item_ids = array($complex_item_ids);
            }
            
            foreach ($complex_item_ids as $complex_item_id)
            {
                $complex_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
                    $complex_item_id);
                
                $page_id = $complex_item->get_parent();
                
                $complex_item->toggle_visibility();
                $succes = $complex_item->update();
            }
            
            $message = $succes ? self :: MESSAGE_VISIBILITY_CHANGED : self :: MESSAGE_VISIBILITY_NOT_CHANGED;
            
            $this->redirect(
                Translation :: get($message), 
                ! $succes, 
                array(
                    self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE, 
                    self :: PARAM_SURVEY_PAGE_ID => $page_id, 
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => ConfigureComponent :: PAGE_QUESTIONS_TAB));
        }
        else
        {
            $this->redirect(
                Translation :: get('NoQuestionSelected'), 
                true, 
                array(
                    self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE, 
                    self :: PARAM_SURVEY_PAGE_ID => $page_id));
        }
    }
}