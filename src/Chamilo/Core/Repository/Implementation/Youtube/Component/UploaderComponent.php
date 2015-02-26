<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Youtube\Form\UploadForm;
use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class UploaderComponent extends Manager
{

    public function run()
    {
        if (! $this->get_external_repository()->get_user_setting('session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            $form = new ExternalObjectForm(ExternalObjectForm :: TYPE_CREATE, $this->get_url(), $this);

            if ($form->validate())
            {
                $upload_token = $form->get_upload_token();

                if ($upload_token)
                {
                    $parameters = $this->get_parameters();
                    $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $parameters[Manager :: PARAM_FEED_TYPE] = Manager :: FEED_TYPE_MYVIDEOS;

                    $platform_url = Redirect :: get_web_link(
                        Path :: getInstance()->getBasePath(true) . 'index.php',
                        $parameters);

                    $next_url = $upload_token['url'] . '?nexturl=' . urlencode($platform_url);
                    $form = new UploadForm($next_url, $upload_token['token']);

                    $html = array();

                    $html[] = $this->render_header();
                    $html[] = $form->toHtml();
                    $html[] = $this->render_footer();

                    return implode("\n", $html);
                }
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode("\n", $html);
            }
        }
    }
}
