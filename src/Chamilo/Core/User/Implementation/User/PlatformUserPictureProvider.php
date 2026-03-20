<?php
namespace Chamilo\Core\User\Implementation\User;

use Chamilo\Core\User\Architecture\Exception\NoPictureForUserException;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use DateTime;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PlatformUserPictureProvider implements UserPictureProviderInterface, UserPictureUpdateProviderInterface
{
    protected Filesystem $filesystem;

    protected FilesystemTools $filesystemTools;

    protected UserService $userService;

    protected WebPathBuilder $webPathBuilder;

    private ConfigurablePathBuilder $configurablePathBuilder;

    private ThemePathBuilder $themeSystemPathBuilder;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, ThemePathBuilder $themeSystemPathBuilder,
        WebPathBuilder $webPathBuilder, Filesystem $filesystem, FilesystemTools $filesystemTools,
        UserService $userService
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
        $this->webPathBuilder = $webPathBuilder;
        $this->filesystem = $filesystem;
        $this->filesystemTools = $filesystemTools;
        $this->userService = $userService;
    }

    public function deleteUserPicture(User $targetUser, User $requestUser): bool
    {
        try {
            if ($this->doesUserHavePicture($targetUser)) {
                $path = $this->getUserPicturePath($targetUser, false);
                $this->getFilesystem()->remove($path);

                $targetUser->setPictureUri(null);

                return $this->getUserService()->updateUser($targetUser);
            }

            return true;
        }
        catch (NoPictureForUserException) {
            return true;
        }
        catch (StorageMethodException) {
            return false;
        }
    }

    public function doesUserHavePicture(User $user): bool
    {
        $uri = $user->getPictureUri();

        return ((strlen($uri) > 0) && ($this->getWebPathBuilder()->isWebUri($uri) || file_exists(
                    $this->getConfigurablePathBuilder()->getProfilePicturePath() . $uri
                )));
    }

    public function downloadUserPicture(User $user): Response
    {
        try {
            $file = $this->getUserPicturePath($user);

            $type = exif_imagetype($file);
            $mime = image_type_to_mime_type($type);
            $size = filesize($file);

            $response = new StreamedResponse();
            $response->headers->add(['Content-Type' => $mime, 'Content-Length' => $size]);
            $response->setPublic();
            $response->setMaxAge(3600 * 24); // 24 hours cache

            $lastModifiedDate = new DateTime('@' . filemtime($file));

            $response->setLastModified($lastModifiedDate);
            $response->setCallback(
                function () use ($file) {
                    readfile($file);
                }
            );

            return $response;
        }
        catch (Exception) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $response;
        }
    }

    /**
     * @return \Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function getPictureAsBase64String(string $filePath): string
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

    public function getUnknownUserPictureAsBase64String(): string
    {
        return $this->getPictureAsBase64String($this->getUnknownUserPicturePath());
    }

    private function getUnknownUserPicturePath(): string
    {
        return $this->getThemeSystemPathBuilder()->getImagePath(Manager::CONTEXT, 'Unknown');
    }

    public function getUserPictureAsBase64String(User $targetUser, User $requestUser): ?string
    {
        try {
            return $this->getPictureAsBase64String($this->getUserPicturePath($targetUser));
        }
        catch (Exception) {
            return null;
        }
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoPictureForUserException
     */
    private function getUserPicturePath(User $user, bool $useFallback = true): string
    {
        if ($this->doesUserHavePicture($user)) {
            return $this->getConfigurablePathBuilder()->getProfilePicturePath() . $user->getPictureUri();
        }
        elseif ($useFallback) {
            return $this->getUnknownUserPicturePath();
        }
        else {
            throw new NoPictureForUserException();
        }
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function setUserPicture(User $targetUser, User $requestUser, ?UploadedFile $fileInformation = null): bool
    {
        if (!$this->deleteUserPicture($targetUser, $requestUser)) {
            return false;
        }

        $path = $this->getConfigurablePathBuilder()->getProfilePicturePath();
        $this->getFilesystem()->mkdir($path);

        $imageFile = $this->getFilesystemTools()->createUniqueName(
            $path, $targetUser->getId() . '-' . $fileInformation->getClientOriginalName()
        );

        move_uploaded_file($fileInformation->getPathname(), $path . $imageFile);

        try {
            $imageManipulation = ImageManipulation::factory($path . $imageFile);
            $imageManipulation->scale(400, 400);

            if (!$imageManipulation->writeToFile()) {
                return false;
            }
        }
        catch (Exception) {
            return false;
        }

        $targetUser->setPictureUri($imageFile);

        return $this->getUserService()->updateUser($targetUser);
    }

    public function updateUserPictureFromParameters(
        User $targetUser, User $requestUser, ?UploadedFile $fileInformation = null, bool $removeExistingPicture = false
    ): bool
    {
        try {
            if ($removeExistingPicture) {
                if (!$this->deleteUserPicture($targetUser, $requestUser)) {
                    return false;
                }
            }
            elseif (!is_null($fileInformation) && strlen($fileInformation->getClientOriginalName()) > 0) {
                if (!$fileInformation->isValid() ||
                    !$this->setUserPicture($targetUser, $requestUser, $fileInformation)) {
                    return false;
                }
            }

            return true;
        }
        catch (StorageMethodException) {
            return false;
        }
    }
}
