<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(self :: PARAM_PUBLICATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $survey_publication = DataManager :: retrieve_by_id(Publication :: class_name(), $id);
                
                if (! Rights :: getInstance()->is_right_granted(
                    Rights :: RIGHT_DELETE,
                    $survey_publication->getId()))
                {
                    $failures ++;
                }
                else
                {
                    if (! $survey_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationsDeleted';
                }
            }
            
            $this->redirect(
                Translation :: get($message), 
                ($failures ? true : false), 
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationsSelected')));
        }
    }
}
?>