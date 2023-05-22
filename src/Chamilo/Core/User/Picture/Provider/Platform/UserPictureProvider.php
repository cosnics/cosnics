<?php
namespace Chamilo\Core\User\Picture\Provider\Platform;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The default user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProvider implements UserPictureProviderInterface, UserPictureUpdateProviderInterface
{
    protected WebPathBuilder $webPathBuilder;

    private ConfigurablePathBuilder $configurablePathBuilder;

    private ThemePathBuilder $themeSystemPathBuilder;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, ThemePathBuilder $themeSystemPathBuilder,
        WebPathBuilder $webPathBuilder
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     *
     * @throws \Exception
     */
    public function deleteUserPicture(User $targetUser, User $requestUser)
    {
        if ($this->doesUserHavePicture($targetUser))
        {
            $path = $this->getUserPicturePath($targetUser);
            Filesystem::remove($path);
            $targetUser->set_picture_uri(null);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function doesUserHavePicture(User $user)
    {
        $uri = $user->get_picture_uri();

        return ((strlen($uri) > 0) && ($this->getWebPathBuilder()->isWebUri($uri) || file_exists(
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
        try
        {
            $file = $this->getUserPicturePath($targetUser);

            $type = exif_imagetype($file);
            $mime = image_type_to_mime_type($type);
            $size = filesize($file);

            $response = new StreamedResponse();
            $response->headers->add(['Content-Type' => $mime, 'Content-Length' => $size]);
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
        catch (Exception $exception)
        {
        }
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function getPictureAsBase64String(string $filePath)
    {
        $type = exif_imagetype($filePath);
        $mime = image_type_to_mime_type($type);

        $fileResource = fopen($filePath, 'r');
        $imageBinary = fread($fileResource, filesize($filePath));
        $imgString = base64_encode($imageBinary);

        fclose($fileResource);

        return 'data:' . $mime . ';base64,' . $imgString;
    }

    public function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return $this->themeSystemPathBuilder;
    }

    /**
     * @return string
     */
    public function getUnknownUserPictureAsBase64String()
    {
        return $this->getPictureAsBase64String($this->getUnknownUserPicturePath());
    }

    /**
     * @return string
     */
    private function getUnknownUserPicturePath()
    {
        return $this->getThemeSystemPathBuilder()->getImagePath(
            'Chamilo\Core\User\Picture\Provider\Platform', 'Unknown', 'png', false
        );
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
        try
        {
            return $this->getPictureAsBase64String($this->getUserPicturePath($targetUser));
        }
        catch (Exception $exception)
        {
            return '';
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param bool $useFallback
     *
     * @return string
     * @throws \Exception
     */
    private function getUserPicturePath(User $user, bool $useFallback = true)
    {
        if ($this->doesUserHavePicture($user))
        {
            return $this->getConfigurablePathBuilder()->getProfilePicturePath() . $user->get_picture_uri();
        }
        elseif ($useFallback)
        {
            return $this->getUnknownUserPicturePath();
        }
        else
        {
            throw new Exception('NoPictureForUser');
        }
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     * @param string[] $fileInformation
     *
     * @throws \Exception
     */
    public function setUserPicture(User $targetUser, User $requestUser, array $fileInformation)
    {
        $this->deleteUserPicture($targetUser, $requestUser);

        $path = $this->getConfigurablePathBuilder()->getProfilePicturePath();
        Filesystem::create_dir($path);

        $imageFile = Filesystem::create_unique_name($path, $targetUser->getId() . '-' . $fileInformation['name']);
        move_uploaded_file($fileInformation['tmp_name'], $path . $imageFile);

        $imageManipulation = ImageManipulation::factory($path . $imageFile);
        $imageManipulation->scale(400, 400);
        $imageManipulation->write_to_file();

        $targetUser->set_picture_uri($imageFile);
    }
}
