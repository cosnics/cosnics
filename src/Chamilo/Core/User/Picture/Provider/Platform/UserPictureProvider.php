<?php

namespace Chamilo\Core\User\Picture\Provider\Platform;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The default user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProvider implements UserPictureProviderInterface
{
    /**
     * Returns the user picture
     *
     * @param User $user
     *
     * @return mixed
     */
    public function downloadUserPicture(User $user)
    {
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

        exit;
    }
}
