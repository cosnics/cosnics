<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Youtube\Form\UploadForm;
use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

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
                $upload_video = $form->upload_video();
                var_dump($upload_video);

                // if ($upload_token)
                // {
                // $parameters = $this->get_parameters();
                // $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                // $parameters[Manager :: PARAM_FEED_TYPE] = Manager :: FEED_TYPE_MYVIDEOS;

                // $redirect = new Redirect($parameters);

                // $next_url = $upload_token['url'] . '?nexturl=' . urlencode($redirect->getUrl());
                // $form = new UploadForm($next_url, $upload_token['token']);

                // $html = array();

                // $html[] = $this->render_header();
                // $html[] = $form->toHtml();
                // $html[] = $this->render_footer();

                // return implode(PHP_EOL, $html);
                // }
                // }
                // else
                // {
                // $html = array();

                // $html[] = $this->render_header();
                // $html[] = $form->toHtml();
                // $html[] = $this->render_footer();

                // return implode(PHP_EOL, $html);
                // }
            }
        }
    }
}
