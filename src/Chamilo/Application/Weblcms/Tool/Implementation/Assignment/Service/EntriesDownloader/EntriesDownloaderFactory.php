<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator;

/**
 * Creates an entries download based on the given strategy
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntriesDownloaderFactory
{
    const ENTRIES_DOWNLOADER_DEFAULT = 'Default';
    const ENTRIES_DOWNLOADER_ENTITY = 'Entity';

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
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator
     */
    protected $archiveCreator;

    /**
     * EntriesDownloader constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $contentObjectPublicationRepository
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository $assignmentPublicationRepository
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator $archiveCreator
     */
    public function __construct(
        \Symfony\Component\Translation\Translator $translator,
        \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $contentObjectPublicationRepository,
        \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository $assignmentPublicationRepository,
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
     * @param string $entriesDownloaderStrategy
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader\EntriesDownloader
     */
    public function getEntriesDownloader($entriesDownloaderStrategy)
    {
        switch ($entriesDownloaderStrategy)
        {
            case self::ENTRIES_DOWNLOADER_DEFAULT:
                return new DefaultEntriesDownloader(
                    $this->translator, $this->contentObjectPublicationRepository,
                    $this->assignmentPublicationRepository, $this->entityServiceManager, $this->assignmentService,
                    $this->archiveCreator
                );
            case self::ENTRIES_DOWNLOADER_ENTITY:
                return new EntityEntriesDownloader(
                    $this->translator, $this->contentObjectPublicationRepository,
                    $this->assignmentPublicationRepository, $this->entityServiceManager, $this->assignmentService,
                    $this->archiveCreator
                );
            default:
                throw new \InvalidArgumentException(
                    sprintf('The given entries downloader strategy %s does not exist', $entriesDownloaderStrategy)
                );
        }
    }
}