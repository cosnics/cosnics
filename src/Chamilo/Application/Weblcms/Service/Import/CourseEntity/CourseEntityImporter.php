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
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;

/**
 * Service to manage the import of course entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseEntityImporter implements CourseEntityImporterInterface
{
    /**
     * @var ImportFormatFactory
     */
    protected $importFormatFactory;

    /**
     * @var WeblcmsRepositoryInterface
     */
    protected $weblcmsRepository;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     *
     * @param ImportFormatFactory $importFormatFactory
     * @param WeblcmsRepositoryInterface $weblcmsRepository
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ImportFormatFactory $importFormatFactory, WeblcmsRepositoryInterface $weblcmsRepository, Translator $translator
    )
    {
        $this->importFormatFactory = $importFormatFactory;
        $this->weblcmsRepository = $weblcmsRepository;
        $this->translator = $translator;
    }

    /**
     * Imports course entities from a given file
     *
     * @param UploadedFile $file
     *
     * @return \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult
     */
    public function importCourseEntitiesFromFile(UploadedFile $file)
    {
        $importFormat = $this->importFormatFactory->getImportFormatForFile($file);

        $importerResult = new CourseEntityRelationImporterResult();
        $importedCourseEntityRelations = $importFormat->parseFile($file, $importerResult);
        $this->handleImportedCourseEntityRelations($importedCourseEntityRelations, $importerResult);

        return $importerResult;
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

        try
        {
            $course =
                $this->getCourseFromImportedCourseEntityRelation($importedCourseEntityRelation, $importDataResult);

            $entityType = null;
            $entityId = null;

            if ($importedCourseEntityRelation instanceof ImportedCourseUserRelation)
            {
                $user =
                    $this->getUserFromImportedCourseEntityRelation($importedCourseEntityRelation, $importDataResult);

                $entityType = CourseEntityRelation::ENTITY_TYPE_USER;
                $entityId = $user->getId();
            }

            if ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
            {
                $group =
                    $this->getGroupFromImportedCourseEntityRelation($importedCourseEntityRelation, $importDataResult);

                $entityType = CourseEntityRelation::ENTITY_TYPE_GROUP;
                $entityId = $group->getId();
            }

            $courseEntityRelation = $this->getCourseEntityRelationFromImported(
                $importedCourseEntityRelation,
                $entityType,
                $entityId,
                $course,
                $importDataResult
            );

            $courseEntityRelation->set_status($importedCourseEntityRelation->getStatusInteger());

            if(!$importDataResult->isCompleted())
            {
                $status =
                    $this->executeCourseEntityRelationAction($importedCourseEntityRelation, $courseEntityRelation);
                if (!$status)
                {
                    $importDataResult->addMessage(
                        $this->translateMessage('ImportCourseEntityRelationFailedDatabaseExecution')
                    );

                    throw new Exception(
                        sprintf(
                            'Failed to handle the course entity relation with course %s, entityType %s and entityId %s',
                            $courseEntityRelation->get_course_id(),
                            $courseEntityRelation->getEntityType(),
                            $courseEntityRelation->getEntityId()
                        )
                    );
                }
            }

            $importDataResult->setSuccessful();
            $importerResult->addSuccessImportDataResult($importDataResult);
        }
        catch (Exception $ex)
        {
            $importDataResult->setFailed();
            $importerResult->addFailedImportDataResult($importDataResult);
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

        $this->weblcmsRepository->clearCourseEntityRelationCache();

        return $status;
    }

    /**
     * Retrieves the course that belongs to a given imported course entity relation
     *
     * @param ImportedCourseEntityRelation $importedCourseEntityRelation
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportDataResult $importDataResult
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     * @throws \Exception
     */
    protected function getCourseFromImportedCourseEntityRelation(
        ImportedCourseEntityRelation $importedCourseEntityRelation, ImportDataResult $importDataResult
    )
    {
        $course = $this->weblcmsRepository->retrieveCourseByCode($importedCourseEntityRelation->getCourseCode());
        if (!$course instanceof Course)
        {
            $importDataResult->addMessage(
                $this->translateMessage(
                    'ImportCourseEntityRelationCourseNotFound',
                    ['{COURSE_CODE}' => $importedCourseEntityRelation->getCourseCode()]
                )
            );

            throw new Exception(
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
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportDataResult $importDataResult
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Exception
     */
    protected function getUserFromImportedCourseEntityRelation(
        ImportedCourseUserRelation $importedCourseEntityRelation, ImportDataResult $importDataResult
    )
    {
        $user = $this->weblcmsRepository->retrieveUserByUsername($importedCourseEntityRelation->getUsername());

        if (!$user instanceof User)
        {
            $importDataResult->addMessage(
                $this->translateMessage(
                    'ImportCourseEntityRelationUserNotFound',
                    ['{USERNAME}' => $importedCourseEntityRelation->getUsername()]
                )
            );

            throw new Exception(
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
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportDataResult $importDataResult
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     * @throws \Exception
     */
    protected function getGroupFromImportedCourseEntityRelation(
        ImportedCourseGroupRelation $importedCourseEntityRelation, ImportDataResult $importDataResult
    )
    {
        $group = $this->weblcmsRepository->retrieveGroupByCode($importedCourseEntityRelation->getGroupCode());

        if (!$group instanceof Group)
        {
            $importDataResult->addMessage(
                $this->translateMessage(
                    'ImportCourseEntityRelationGroupNotFound',
                    ['{GROUP_CODE}' => $importedCourseEntityRelation->getGroupCode()]
                )
            );

            throw new Exception(
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
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportDataResult $importDataResult
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation
     * @throws \Exception
     */
    protected function getCourseEntityRelationFromImported(
        ImportedCourseEntityRelation $importedCourseEntityRelation,
        $entityType, $entityId, $course, ImportDataResult $importDataResult
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
                    $importDataResult->addMessage(
                        $this->translateMessage(
                            'ImportCourseEntityRelationCourseUserRelationNotFound',
                            [
                                '{COURSE_CODE}' => $importedCourseEntityRelation->getCourseCode(),
                                '{USERNAME}' => $importedCourseEntityRelation->getUsername()
                            ]
                        )
                    );

                    throw new Exception(
                        sprintf(
                            'Could not find a valid relation object for course %s and user %s',
                            $course->get_visual_code(),
                            $importedCourseEntityRelation->getUsername()
                        )
                    );
                }
                elseif ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
                {
                    $importDataResult->addMessage(
                        $this->translateMessage(
                            'ImportCourseEntityRelationCourseGroupRelationNotFound',
                            [
                                '{COURSE_CODE}' => $importedCourseEntityRelation->getCourseCode(),
                                '{GROUP_CODE}' => $importedCourseEntityRelation->getGroupCode()
                            ]
                        )
                    );

                    throw new Exception(
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
                    $importDataResult->addMessage(
                        $this->translateMessage(
                            'ImportCourseEntityRelationCourseUserRelationAlreadyCreated',
                            [
                                '{COURSE_CODE}' => $importedCourseEntityRelation->getCourseCode(),
                                '{USERNAME}' => $importedCourseEntityRelation->getUsername()
                            ]
                        )
                    );

                    $importDataResult->setSuccessful();
                }
                elseif ($importedCourseEntityRelation instanceof ImportedCourseGroupRelation)
                {
                    $importDataResult->addMessage(
                        $this->translateMessage(
                            'ImportCourseEntityRelationCourseGroupRelationAlreadyCreated',
                            [
                                '{COURSE_CODE}' => $importedCourseEntityRelation->getCourseCode(),
                                '{GROUP_CODE}' => $importedCourseEntityRelation->getGroupCode()
                            ]
                        )
                    );

                    $importDataResult->setSuccessful();
                }
            }
            else
            {
                $courseEntityRelation = new CourseEntityRelation();

                $courseEntityRelation->set_course_id($course->getId());
                $courseEntityRelation->setEntityId($entityId);
                $courseEntityRelation->setEntityType($entityType);
            }
        }

        return $courseEntityRelation;
    }

    /**
     * Translates a given message, with optionally the given parameters
     *
     * @param string $message
     * @param array $parameters
     *
     * @return string
     */
    protected function translateMessage($message, $parameters = [])
    {
        return $this->translator->trans($message, $parameters, 'Chamilo\\Application\\Weblcms');
    }
}