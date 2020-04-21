<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Compression\ArchiveCreator\Archive;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntriesDownloader
{
    const TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Assignment';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository
     */
    protected $contentObjectPublicationRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    protected $assignmentPublicationRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator
     */
    protected $archiveCreator;

    /**
     * @var ArchiveFolder[]
     */
    protected $entityFoldersCache;

    /**
     * @var ArchiveFolder[]
     */
    protected $entityAssignmentsFoldersCache;

    /**
     * EntriesDownloader constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $contentObjectPublicationRepository
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository $assignmentPublicationRepository
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator $archiveCreator
     */
    public function __construct(
        Translator $translator,
        \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $contentObjectPublicationRepository,
        PublicationRepository $assignmentPublicationRepository,
        EntityServiceManager $entityServiceManager, AssignmentService $assignmentService, ArchiveCreator $archiveCreator
    )
    {
        $this->translator = $translator;
        $this->contentObjectPublicationRepository = $contentObjectPublicationRepository;
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->entityServiceManager = $entityServiceManager;
        $this->assignmentService = $assignmentService;
        $this->archiveCreator = $archiveCreator;
    }

    /**
     * @param array $contentObjectPublicationIdentifiers
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function downloadEntriesForAssignmentsByPublicationIdentifiers(
        $contentObjectPublicationIdentifiers = [], Request $request, Course $course
    )
    {
        if (empty($contentObjectPublicationIdentifiers))
        {
            throw new NoObjectSelectedException(
                $this->translator->trans('ContentObjectPublication', [], self::TRANSLATION_CONTEXT)
            );
        }

        $publications =
            $this->contentObjectPublicationRepository->findPublicationsbyIds($contentObjectPublicationIdentifiers);

        if (empty($publications))
        {
            throw new NoObjectSelectedException(
                $this->translator->trans('ContentObjectPublication', [], self::TRANSLATION_CONTEXT)
            );
        }

        $this->downloadEntriesForAssignments($publications, $request, $course);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[] $contentObjectPublications
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function downloadEntriesForAssignments($contentObjectPublications, Request $request, Course $course)
    {
        $archive = new Archive();
        $archive->setName(
            $this->translator->trans(
                'AssignmentEntries', ['{COURSE_CODE}' => $course->get_visual_code()], self::TRANSLATION_CONTEXT
            )
        );

        foreach ($contentObjectPublications as $publication)
        {
            $assignmentPublication =
                $this->assignmentPublicationRepository->findPublicationByContentObjectPublication($publication);

            /** @var Assignment $assignment */
            $assignment = $publication->getContentObject();

            $entries = $this->assignmentService->findEntriesByContentObjectPublication(
                $publication, $assignmentPublication->getEntityType()
            );

            $this->handleAssignment($assignment, $entries, $archive);
        }

        $this->archiveCreator->createAndDownloadArchive($archive, $request);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[] $entries
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     *
     * @return mixed
     */
    abstract function handleAssignment(Assignment $assignment, $entries, Archive $archive);

    /**
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder $parentFolder
     *
     * @return ArchiveFolder
     */
    protected function getOrCreateFolderByEntity($entityType, $entityId, ArchiveFolder $parentFolder)
    {
        $cacheKey = md5($entityType . '-' . $entityId);
        if (!array_key_exists($cacheKey, $this->entityFoldersCache))
        {
            $folder = new ArchiveFolder();

            $folder->setName(
                $this->entityServiceManager->getEntityServiceByType($entityType)->renderEntityNameById($entityId)
            );

            $parentFolder->addItem($folder);

            $this->entityFoldersCache[$cacheKey] = $folder;
        }

        return $this->entityFoldersCache[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder $parentFolder
     *
     * @return \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder
     */
    protected function getOrCreateFolderByAssignmentAndEntity(
        Assignment $assignment, $entityType, $entityId, ArchiveFolder $parentFolder
    )
    {
        $cacheKey = md5($assignment->getId() . '-' . $entityType . '-' . $entityId);
        if (!array_key_exists($cacheKey, $this->entityAssignmentsFoldersCache))
        {
            $folder = new ArchiveFolder();
            $folder->setName($assignment->get_title());

            $parentFolder->addItem($folder);

            $this->entityAssignmentsFoldersCache[$cacheKey] = $folder;
        }

        return $this->entityAssignmentsFoldersCache[$cacheKey];
    }
}
