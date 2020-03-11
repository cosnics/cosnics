<?php
namespace Chamilo\Core\User\Picture\Provider\Platform;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use DateTime;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The default user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProvider implements UserPictureProviderInterface
{
    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder, Theme $themeUtilities)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     *
     * @return boolean
     */
    private function doesUserHavePicture(User $targetUser)
    {
        $uri = $targetUser->get_picture_uri();

        return ((strlen($uri) > 0) && (Path::getInstance()->isWebUri($uri) || file_exists(
                    $this->getConfigurablePathBuilder()->getProfilePicturePath() . $uri
                )));
    }

    /**
     * Downloads the user picture
     *
     * @param User $targetUser
     * @param User $requestUser
     *
     * @throws \Exception
     */
    public function downloadUserPicture(User $targetUser, User $requestUser)
    {
        $file = $this->getUserPicturePath($targetUser);

        $type = exif_imagetype($file);
        $mime = image_type_to_mime_type($type);
        $size = filesize($file);

        $response = new StreamedResponse();
        $response->headers->add(array('Content-Type' => $mime, 'Content-Length' => $size));
        $response->setPublic();
        $response->setMaxAge(3600 * 24); // 24 hours cache

        $lastModifiedDate = new DateTime();
        $lastModifiedDate->setTimestamp(filemtime($file));

        $response->setLastModified($lastModifiedDate);
        $response->setCallback(
            function () use ($file) {
                readfile($file);
            }
        );

        $response->send();

        exit();
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     *
     * @return UserPictureProvider
     */
    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder): UserPictureProvider
    {
        $this->configurablePathBuilder = $configurablePathBuilder;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities(): Theme
    {
        return $this->themeUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     *
     * @return UserPictureProvider
     */
    public function setThemeUtilities(Theme $themeUtilities): UserPictureProvider
    {
        $this->themeUtilities = $themeUtilities;

        return $this;
    }

    /**
     * Downloads the user picture
     *
     * @param User $targetUser
     * @param User $requestUser
     *
     * @return string
     */
    public function getUserPictureAsBase64String(User $targetUser, User $requestUser)
    {
        $file = $this->getUserPicturePath($targetUser);

        $type = exif_imagetype($file);
        $mime = image_type_to_mime_type($type);

        $fileResource = fopen($file, "r");
        $imageBinary = fread($fileResource, filesize($file));
        $imgString = base64_encode($imageBinary);

        fclose($fileResource);

        return 'data:' . $mime . ';base64,' . $imgString;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    private function getUserPicturePath(User $user)
    {
        if ($this->doesUserHavePicture($user))
        {
            return $this->getConfigurablePathBuilder()->getProfilePicturePath() . $user->get_picture_uri();
        }
        else
        {
            return $this->getThemeUtilities()->getImagePath(
                'Chamilo\Core\User\Picture\Provider\Platform', 'Unknown', 'png', false
            );
        }
    }
}
