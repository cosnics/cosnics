<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository;
use RuntimeException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentPublicationService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    protected $assignmentPublicationRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * AssignmentPublicationService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository $assignmentPublicationRepository
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     */
    public function __construct(
        PublicationRepository $assignmentPublicationRepository, AssignmentService $assignmentService
    )
    {
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->assignmentService = $assignmentService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function deletePublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->assignmentService->deleteEntriesForContentObjectPublication($contentObjectPublication);
        $this->assignmentPublicationRepository->deletePublicationForContentObjectPublication($contentObjectPublication);

        if (!$contentObjectPublication->delete())
        {
            throw new RuntimeException(
                sprintf(
                    'The given content object publication with id %s could not be deleted',
                    $contentObjectPublication->getId()
                )
            );
        }
    }

}
