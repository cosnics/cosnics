<?php

namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseEntityRelation;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseGroupRelation;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseUserRelation;
use Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format\ImportFormatFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\WeblcmsRepositoryInterface;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Domain\UserImporter\ImportDataResult;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Service to manage the import of course entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseEntityImporter implements CourseEntityImporterInterface
{

    /**
     *
     * @var ImportFormatFactory
     */
    protected $importFormatFactory;

    /**
     *
     * @var WeblcmsRepositoryInterface
     */
    protected $weblcmsRepository;

    /**
     *
     * @param ImportFormatFactory $importFormatFactory
     * @param WeblcmsRepositoryInterface $weblcmsRepository
     */
    public function __construct(ImportFormatFactory $importFormatFactory, WeblcmsRepositoryInterface $weblcmsRepository)
    {
        $this->importFormatFactory = $importFormatFactory;
        $this->weblcmsRepository = $weblcmsRepository;
    }

    /**
     * Imports course entities from a given file
     *
     * @param UploadedFile $file
     */
    public function importCourseEntitiesFromFile(UploadedFile $file)
    {
        $importFormat = $this->importFormatFactory->getImportFormatForFile($file);

        $importerResult = new CourseEntityRelationImporterResult();
        $importedCourseEntityRelations = $importFormat->parseFile($file, $importerResult);
        $this->handleImportedCourseEntityRelations($importedCourseEntityRelations, $importerResult);
    }

    /**
     * Handles the imported course entity relations
     *
     * @param ImportedCourseEntityRelation[] $importedCourseEntityRelations
     * @param \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult $importerResult
     */
    protected function handleImportedCourseEntityRelations(
        array $importedCourseEntityRelations, CourseEntityRelationImporterResult $importerResult
    )
    {
        foreach ($importedCourseEntityRelations as $importedCourseEntityRelation)
        {
            $this->handleImportedCourseEntityRelation($importedCourseEntityRelation, $importerResult);
        }
    }

    /**
     * Handles an imported course entity relation
     *
     * @param ImportedCourseEntityRelation $importedCourseEntityRelation
     * @param \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult $importerResult
     *
     * @throws \Exception
     */
    protected function handleImportedCourseEntityRelation(
        ImportedCourseEntityRelation $importedCourseEntityRelation, CourseEntityRelationImporterResult $importerResult
    )
    {
        $importDataResult = new ImportDataResult($importedCourseEntityRelation);

        $course = $this->getCourseFromImportedCourseEntityRelation($importedCourseEntityRelation);

        $entityType = null;
        $entityId = null;

        if ($importedCourseEntityRelation instanceof ImportedCourseUserRelation)
        {
            $user = $this->getUserFromImportedCourseEntityRelation($importedCourseEntityRelation);

            $entityType = CourseEntityRelation::ENTITY_TYPE_USER;
            $entityId = $user->getId();
        }

        if ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
        {
            $group = $this->getGroupFromImportedCourseEntityRelation($importedCourseEntityRelation);

            $entityType = CourseEntityRelation::ENTITY_TYPE_GROUP;
            $entityId = $group->getId();
        }

        $courseEntityRelation = $this->getCourseEntityRelationFromImported(
            $importedCourseEntityRelation,
            $entityType,
            $entityId,
            $course
        );

        $courseEntityRelation->set_status($importedCourseEntityRelation->getStatusInteger());

        $status = $this->executeCourseEntityRelationAction($importedCourseEntityRelation, $courseEntityRelation);
        if (!$status)
        {
            throw new \Exception(
                sprintf(
                    'Failed to handle the course entity relation with course %s, entityType %s and entityId %s',
                    $courseEntityRelation->get_course_id(),
                    $courseEntityRelation->getEntityType(),
                    $courseEntityRelation->getEntityId()
                )
            );
        }
    }

    /**
     * Executes the import course entity relation action
     *
     * @param ImportedCourseEntityRelation $importedCourseEntityRelation
     * @param CourseEntityRelation $courseEntityRelation
     *
     * @return bool
     */
    protected function executeCourseEntityRelationAction(
        ImportedCourseEntityRelation $importedCourseEntityRelation,
        CourseEntityRelation $courseEntityRelation
    )
    {
        $status = false;

        if ($importedCourseEntityRelation->isNew())
        {
            $status = $courseEntityRelation->create();
        }
        elseif ($importedCourseEntityRelation->isUpdate())
        {
            $status = $courseEntityRelation->update();
        }
        elseif ($importedCourseEntityRelation->isDelete())
        {
            $status = $courseEntityRelation->delete();
        }

        return $status;
    }

    /**
     * Retrieves the course that belongs to a given imported course entity relation
     *
     * @param ImportedCourseEntityRelation $importedCourseEntityRelation
     *
     * @return Course
     *
     * @throws \Exception
     */
    protected function getCourseFromImportedCourseEntityRelation(
        ImportedCourseEntityRelation $importedCourseEntityRelation
    )
    {
        $course = $this->weblcmsRepository->retrieveCourseByCode($importedCourseEntityRelation->getCourseCode());
        if (!$course instanceof Course)
        {
            throw new \Exception(
                sprintf(
                    'The given course with code "%s" could not be found',
                    $importedCourseEntityRelation->getCourseCode()
                )
            );
        }

        return $course;
    }

    /**
     * Retrieves the user that belongs to a given imported course entity relation
     *
     * @param ImportedCourseUserRelation $importedCourseEntityRelation
     *
     * @return User
     *
     * @throws \Exception
     */
    protected function getUserFromImportedCourseEntityRelation(ImportedCourseUserRelation $importedCourseEntityRelation)
    {
        $user = $this->weblcmsRepository->retrieveUserByUsername($importedCourseEntityRelation->getUsername());

        if (!$user instanceof User)
        {
            throw new \Exception(
                sprintf(
                    'The given user with username "%s" could not be found',
                    $importedCourseEntityRelation->getUsername()
                )
            );
        }

        return $user;
    }

    /**
     * Retrieves the group that belongs to a given imported course entity relation
     *
     * @param ImportedCourseGroupRelation $importedCourseEntityRelation
     *
     * @return Group
     * @throws \Exception
     */
    protected function getGroupFromImportedCourseEntityRelation(
        ImportedCourseGroupRelation $importedCourseEntityRelation
    )
    {
        $group = $this->weblcmsRepository->retrieveGroupByCode($importedCourseEntityRelation->getGroupCode());

        if (!$group instanceof Group)
        {
            throw new \Exception(
                sprintf(
                    'The given group with code "%s" could not be found',
                    $importedCourseEntityRelation->getGroupCode()
                )
            );
        }

        return $group;
    }

    /**
     * Returns an existing or a new CourseEntityRelation object based on the given imported course entity relation
     * and a few shortcut properties.
     *
     * @param ImportedCourseEntityRelation $importedCourseEntityRelation
     * @param int $entityType
     * @param int $entityId
     * @param Course $course
     *
     * @return CourseEntityRelation
     *
     * @throws \Exception
     */
    protected function getCourseEntityRelationFromImported(
        ImportedCourseEntityRelation $importedCourseEntityRelation,
        $entityType, $entityId, $course
    )
    {
        $courseEntityRelation = $this->weblcmsRepository->retrieveCourseEntityRelationByEntityAndCourse(
            $entityType,
            $entityId,
            $course->getId()
        );

        if ($importedCourseEntityRelation->isUpdate() || $importedCourseEntityRelation->isDelete())
        {
            if (!$courseEntityRelation instanceof CourseEntityRelation)
            {
                if ($importedCourseEntityRelation instanceof ImportedCourseUserRelation)
                {
                    throw new \Exception(
                        sprintf(
                            'Could not find a valid relation object for course %s and user %s',
                            $course->get_visual_code(),
                            $importedCourseEntityRelation->getUsername()
                        )
                    );
                }
                elseif ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
                {
                    throw new \Exception(
                        sprintf(
                            'Could not find a valid relation object for course %s and group %s',
                            $course->get_visual_code(),
                            $importedCourseEntityRelation->getGroupCode()
                        )
                    );
                }
            }
        }
        elseif ($importedCourseEntityRelation->isNew())
        {
            if ($courseEntityRelation instanceof CourseEntityRelation)
            {
                if ($importedCourseEntityRelation instanceof ImportedCourseUserRelation)
                {
                    throw new \Exception(
                        sprintf(
                            'A relation object for course %s and user %s is already created',
                            $course->get_visual_code(),
                            $importedCourseEntityRelation->getUsername()
                        )
                    );
                }
                elseif ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
                {
                    throw new \Exception(
                        sprintf(
                            'A relation object for course %s and group %s is already created',
                            $course->get_visual_code(),
                            $importedCourseEntityRelation->getGroupCode()
                        )
                    );
                }
            }

            $courseEntityRelation = new CourseEntityRelation();

            $courseEntityRelation->set_course_id($course->getId());
            $courseEntityRelation->setEntityId($entityId);
            $courseEntityRelation->setEntityType($entityType);
        }

        return $courseEntityRelation;
    }
}