<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

class UploaderComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_external_repository()->get_user_setting(Session :: get_user_id(), 'session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            $form = new ExternalObjectForm(ExternalObjectForm :: TYPE_CREATE, $this->get_url(), $this);

            if ($form->validate())
            {
                $id = $form->upload_video();

                if ($id)
                {
                    $parameters = $this->get_parameters();
                    $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                    $parameters[Manager :: PARAM_FEED_TYPE] = Manager :: FEED_TYPE_MYVIDEOS;

                    $redirect = new Redirect($parameters);

                    $redirect->toUrl();
                }
                else
                {
                    Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('YoutubeUploadProblem'));

                    $html = array();

                    $html[] = $this->render_header();
                    $html[] = $form->toHtml();
                    $html[] = $this->render_footer();

                    return implode(PHP_EOL, $html);
                }
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
    }
}
