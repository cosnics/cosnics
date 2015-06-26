<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 * $Id: edit.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.component
 * @deprecated Use the content_object_updater and publication_updater
 */
class UpdaterComponent extends Manager
{

    public function run()
    {
        $pid = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID) ? Request :: get(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID) : $_POST[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID];
        if (is_null($pid))
        {
            $this->redirect(
                Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES), 
                '', 
                array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $pid);
        
        if (is_null($publication))
        {
            $this->redirect(
                Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES), 
                '', 
                array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $publication))
        {
            $content_object = $publication->get_content_object();
            
            $form = ContentObjectForm :: factory(
                ContentObjectForm :: TYPE_EDIT, 
                new PersonalWorkspace($this->get_user()), 
                $content_object, 
                'edit', 
                'post', 
                $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $pid)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                    if ($form->is_version())
                    {
                        $publication->set_content_object_id($content_object->get_latest_version()->get_id());
                        $publication->update();
                        $message = htmlentities(
                            Translation :: get(
                                'ObjectUpdated', 
                                array('OBJECT' => Translation :: get('Publication')), 
                                Utilities :: COMMON_LIBRARIES));
                        $this->redirect($message, false);
                    }
                }
                
                $this->redirect($message, false);
            }
            else
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $this->redirect(
                Translation :: get("NotAllowed"), 
                '', 
                array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('courses general');
    }
}
