<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DeleterComponent extends Manager
{

    /**
     * Modified version of the default Deleter to allow for the feedback-functionality
     */
    public function run()
    {
        if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_ids = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids = $_POST[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID];
        }
        
        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }
        
        $failures = 0;
        
        foreach ($publication_ids as $pid)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class, 
                $pid);
            
            $content_object = $publication->get_content_object();
            
            if ($content_object->get_type() == Introduction::class_name())
            {
                $publication->ignore_display_order();
            }
            
            if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
            {
                if ($publication->delete())
                {
                    $parameters = new DataClassRetrieveParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                Publication::class_name(), 
                                Publication::PROPERTY_PUBLICATION_ID), 
                            new StaticConditionVariable($publication->get_id())));
                    $assessment_publication = DataManager::retrieve(Publication::class, $parameters);
                    if (! $assessment_publication->delete())
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            else
            {
                $failures ++;
            }
        }
        
        if ($failures == 0)
        {
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationDeleted'));
            }
        }
        else
        {
            $message = htmlentities(Translation::get('ContentObjectPublicationsNotDeleted'));
        }
        
        $this->redirect(
            $message, 
            $failures !== 0, 
            array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null));
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
