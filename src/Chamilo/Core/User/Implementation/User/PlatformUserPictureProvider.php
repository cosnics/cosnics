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
use Chamilo\Libraries\Filesystem\Service\ImageConverter;
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
    public function __construct(
        protected ConfigurablePathBuilder $configurablePathBuilder, protected ThemePathBuilder $themeSystemPathBuilder,
        protected WebPathBuilder $webPathBuilder, protected Filesystem $filesystem,
        protected FilesystemTools $filesystemTools, protected UserService $userService,
        protected ImageConverter $imageConverter
    )
    {
    }

    public function deleteUserPicture(User $user, ?User $executingUser = null): bool
    {
        try {
            if ($this->doesUserHavePicture($user)) {
                $path = $this->getUserPicturePath($user, false);
                $this->filesystem->remove($path);

                $user->setPictureUri(null);

                return $this->userService->updateUser($user, $executingUser);
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

        return ((strlen($uri) > 0) && ($this->webPathBuilder->isWebUri($uri) || file_exists(
                    $this->configurablePathBuilder->getProfilePicturePath() . $uri
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

    public function getUnknownUserPictureAsBase64String(): string
    {
        return $this->imageConverter->getPictureAsBase64String($this->getUnknownUserPicturePath());
    }

    private function getUnknownUserPicturePath(): string
    {
        return $this->themeSystemPathBuilder->getImagePath(Manager::CONTEXT, 'Unknown');
    }

    public function getUserPictureAsBase64String(User $user, bool $useFallback = true): ?string
    {
        try {
            return $this->imageConverter->getPictureAsBase64String($this->getUserPicturePath($user, $useFallback));
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
            return $this->configurablePathBuilder->getProfilePicturePath() . $user->getPictureUri();
        }
        elseif ($useFallback) {
            return $this->getUnknownUserPicturePath();
        }
        else {
            throw new NoPictureForUserException();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function setUserPicture(User $user, ?UploadedFile $fileInformation = null, ?User $executingUser = null): bool
    {
        if (!$this->deleteUserPicture($user, $executingUser)) {
            return false;
        }

        $path = $this->configurablePathBuilder->getProfilePicturePath();
        $this->filesystem->mkdir($path);

        $imageFile = $this->filesystemTools->createUniqueName(
            $path, $user->getId() . '-' . $fileInformation->getClientOriginalName()
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

        $user->setPictureUri($imageFile);

        return $this->userService->updateUser($user, $executingUser);
    }

    public function updateUserPictureFromParameters(
        User $user, ?UploadedFile $fileInformation = null, bool $removeExistingPicture = false,
        ?User $executingUser = null
    ): bool
    {
        try {
            if ($removeExistingPicture) {
                if (!$this->deleteUserPicture($user, $executingUser)) {
                    return false;
                }
            }
            elseif (!is_null($fileInformation) && strlen($fileInformation->getClientOriginalName()) > 0) {
                if (!$fileInformation->isValid() || !$this->setUserPicture($user, $fileInformation)) {
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
