<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Toolcomponent to sent email of an alredy published publication.
 * Will only send a publication once!
 */
class PublicationMailerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        
        if (isset($publication_id))
        {
            
            $failure = false;
            
            /**
             *
             * @var ContentObjectPublication $publication
             */
            $publication = DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(), 
                $publication_id);
            
            // currently: publications only sent once! Maybe this is not necessary...
            if ($publication->is_email_sent())
            {
                $message = htmlentities(Translation::get('EmailAlreadySent'));
                $failure = true;
            }
            else
            {
                $publication->mail_publication(true);
                $message = Translation::get('EmailSent');
            }
            
            $params = array();
            $params['tool_action'] = null;
            if (Request::get('details') == 1)
            {
                $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $publication_id;
                $params['tool_action'] = 'view';
            }
            
            $this->redirect($message, $failure, $params);
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->display_error_message(Translation::get('NoObjectsSelected'));
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
