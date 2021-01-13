<?php

namespace Chamilo\Application\ExamAssignment\Service;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExamUploaderService
{
    /**
     * @var ExamAssignmentService
     */
    protected $examAssignmentService;

    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * ExamUploaderService constructor.
     *
     * @param ExamAssignmentService $examAssignmentService
     * @param AssignmentService $assignmentService
     * @param PublicationService $publicationService
     */
    public function __construct(
        ExamAssignmentService $examAssignmentService, AssignmentService $assignmentService,
        PublicationService $publicationService
    )
    {
        $this->examAssignmentService = $examAssignmentService;
        $this->assignmentService = $assignmentService;
        $this->publicationService = $publicationService;
    }

    /**
     * @param User $user
     * @param int $contentObjectPublicationId
     * @param UploadedFile $uploadedFile
     *
     * @param string|null $securityCode
     *
     * @param string|null $ipaddress
     *
     * @throws NotAllowedException
     */
    public function uploadFileToAssignment(
        User $user, int $contentObjectPublicationId, UploadedFile $uploadedFile,
        string $securityCode = null, string $ipaddress = null
    )
    {
        if (!$this->examAssignmentService->canUserSubmit($user, $contentObjectPublicationId, $securityCode))
        {
            throw new NotAllowedException();
        }

        $file = $this->createFile($user, $uploadedFile);
        $contentObjectPublication = $this->publicationService->getPublication($contentObjectPublicationId);
        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            throw new \RuntimeException(
                sprintf('Content object publication with id %s not found', $contentObjectPublicationId)
            );
        }

        $this->assignmentService->createEntry(
            $contentObjectPublication, Entry::ENTITY_TYPE_USER, $user->getId(), $user->getId(), $file->getId(),
            $ipaddress
        );
    }

    /**
     * @param User $user
     * @param UploadedFile $uploadedFile
     *
     * @return File
     * @throws \RuntimeException
     */
    protected function createFile(User $user, UploadedFile $uploadedFile)
    {
        $file = File::fromUploadedFile($user, $uploadedFile);

        if (!$file->create())
        {
            throw new \RuntimeException("Could not create file");
        }

        return $file;
    }

}
