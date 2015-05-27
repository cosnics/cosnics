<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserPictureComponent extends \Chamilo\Core\User\Ajax\Manager
{

    public function run()
    {
        $userId = $this->getRequest()->query->get(\Chamilo\Core\User\Manager :: PARAM_USER_USER_ID);
        $user = DataManager :: retrieve_by_id(\Chamilo\Core\User\Storage\DataClass\User :: class_name(), $userId);

        $file = $user->get_full_picture_path();

        $type = exif_imagetype($file);
        $mime = image_type_to_mime_type($type);
        $size = filesize($file);

        $response = new StreamedResponse();
        $response->headers->add(array('Content-Type' => $mime, 'Content-Length' => $size));
        $response->setCallback(function () use($file)
        {
            readfile($file);
        });

        $response->send();
    }
}