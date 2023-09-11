<?php
namespace Chamilo\Application\Weblcms\Service\Publication;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Domain\Publication\PublicationTarget;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    protected AssignmentPublicationService $assignmentPublicationService;

    protected AssignmentService $assignmentService;

    protected ConfigurationConsulter $configurationConsulter;

    protected AssignmentPublicationService $learningPathAssignmentPublicationService;

    protected LearningPathAssignmentService $learningPathAssignmentService;

    protected UserService $userService;

    private PublicationTargetRenderer $publicationTargetRenderer;

    private PublicationTargetService $publicationTargetService;

    private Translator $translator;

    public function __construct(
        AssignmentService $assignmentService, LearningPathAssignmentService $learningPathAssignmentService,
        UserService $userService, Translator $translator, PublicationTargetService $publicationTargetService,
        PublicationTargetRenderer $publicationTargetRenderer,
        AssignmentPublicationService $assignmentPublicationService,
        AssignmentPublicationService $learningPathAssignmentPublicationService,
        ConfigurationConsulter $configurationConsulter
    )
    {
        $this->assignmentService = $assignmentService;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->publicationTargetService = $publicationTargetService;
        $this->publicationTargetRenderer = $publicationTargetRenderer;
        $this->assignmentPublicationService = $assignmentPublicationService;
        $this->learningPathAssignmentPublicationService = $learningPathAssignmentPublicationService;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    ): void
    {
        $type = $contentObject->getType();

        $excludedCourseTypeSetting = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms', 'excluded_course_types']
        );

        $condition = null;

        if (!empty($excludedCourseTypeSetting))
        {
            $excludedCourseTypes = explode(',', $excludedCourseTypeSetting);

            $condition = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_COURSE_TYPE_ID), $excludedCourseTypes
                )
            );
        }

        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_courses_from_user(
            $user, $condition
        );

        $possible_courses = [];

        foreach ($courses as $course)
        {
            if ($course->is_course_admin($user))
            {
                $possible_courses[] = $course;
            }
        }

        $course_settings_controller = CourseSettingsController::getInstance();
        $course_management_rights = CourseManagementRights::getInstance();

        $tools = DataManager::retrieves(CourseTool::class, new DataClassRetrievesParameters());

        $toolNames = [];
        $types = [];

        foreach ($tools as $tool)
        {
            $class = $tool->getContext() . '\Manager';

            if (class_exists($class))
            {
                $allowed_types = $class::get_allowed_types();

                if (count($allowed_types) > 0)
                {
                    $types[$tool->getId()] = $allowed_types;
                    $toolNames[$tool->getId()] = $tool->get_name();
                }

                if (is_subclass_of(
                    $class, 'Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface'
                ))
                {
                    $types[$tool->getId()][] = Introduction::class;
                    $toolNames[$tool->getId()] = $tool->get_name();
                }
            }
        }

        $columnNames = [];
        $columnNames[] = $this->getTranslator()->trans('Course', [], \Chamilo\Application\Weblcms\Manager::CONTEXT);
        $columnNames[] = $this->getTranslator()->trans('Tool', [], \Chamilo\Application\Weblcms\Manager::CONTEXT);

        $this->getPublicationTargetRenderer()->addHeaderToForm(
            $form, $this->getTranslator()->trans('TypeName', [], 'Chamilo\Application\Weblcms'), $columnNames
        );

        $modifierServiceKey =
            $this->getPublicationTargetService()->addModifierServiceIdentifierAndGetKey(PublicationModifier::class);

        foreach ($types as $tool_id => $allowed_types)
        {
            if (in_array($type, $allowed_types))
            {
                foreach ($possible_courses as $course)
                {
                    if ($type == Introduction::class && (!$course_settings_controller->get_course_setting(
                                $course, CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
                            ) || !empty(
                            DataManager::retrieve_introduction_publication_by_course_and_tool(
                                $course->getId(), $toolNames[$tool_id]
                            )
                            )))
                    {
                        continue;
                    }

                    if ($course_settings_controller->get_course_setting(
                            $course, CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $tool_id
                        ) && $course_management_rights->is_allowed_management(
                            CourseManagementRights::PUBLISH_FROM_REPOSITORY_RIGHT, $course->getId()
                        ))
                    {
                        $tool_namespace = Manager::get_tool_type_namespace(
                            $toolNames[$tool_id]
                        );
                        $tool_name = $this->getTranslator()->trans('TypeName', [], $tool_namespace);

                        $publicationTargetKey = $this->getPublicationTargetService()->addPublicationTargetAndGetKey(
                            new PublicationTarget(
                                PublicationModifier::class, $course->getId(), $toolNames[$tool_id], $user->getId()
                            )
                        );

                        $targetNames = [];
                        $targetNames[] = $course->get_title() . ' (' . $course->get_visual_code() . ')';
                        $targetNames[] = $tool_name;

                        $this->getPublicationTargetRenderer()->addPublicationTargetToForm(
                            $form, $modifierServiceKey, $publicationTargetKey, $targetNames
                        );
                    }
                }
            }
        }

        $this->getPublicationTargetRenderer()->addFooterToForm($form);
        $this->getPublicationTargetRenderer()->addPublicationAttributes($form, $modifierServiceKey);
    }

    /**
     * @param int[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        if (DataManager::areContentObjectsPublished($contentObjectIdentifiers))
        {
            return true;
        }

        $assignmentPublicationServices = $this->getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            if ($assignmentPublicationService->areContentObjectsPublished($contentObjectIdentifiers))
            {
                return true;
            }
        }

        return false;
    }

    public function canContentObjectBeEdited(string $contentObjectIdentifier): bool
    {
        $contentObject = new ContentObject();
        $contentObject->setId($contentObjectIdentifier);

        if ($this->getAssignmentService()->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        if ($this->getLearningPathAssignmentService()->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        return true;
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        $user = $this->getUserService()->findUserByIdentifier((string) $contentObject->get_owner_id());
        $isTeacher = ($user instanceof User && $user->get_status() == User::STATUS_TEACHER);

        $assignmentService = $this->getAssignmentService();

        if ($assignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher && !$assignmentService->isContentObjectOwnerSameAsSubmitter($contentObject));
        }

        $learningPathAssignmentService = $this->getLearningPathAssignmentService();

        if ($learningPathAssignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher && !$learningPathAssignmentService->isContentObjectOwnerSameAsSubmitter($contentObject));
        }

        return true;
    }

    public function countPublicationAttributes(int $type, string $objectIdentifier, ?Condition $condition = null): int
    {
        $count = DataManager::countPublicationAttributes($type, $objectIdentifier, $condition);

        $assignmentPublicationServices = $this->getAssignmentPublicationServices();

        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            $count += match ($type)
            {
                self::ATTRIBUTES_TYPE_USER => $assignmentPublicationService->countContentObjectPublicationAttributesForUser(
                    $objectIdentifier
                ),
                default => $assignmentPublicationService->countContentObjectPublicationAttributesForContentObject(
                    $objectIdentifier
                ),
            };
        }

        return $count;
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        if (!DataManager::deleteContentObjectPublications($contentObject->getId()))
        {
            return false;
        }

        try
        {
            $assignmentPublicationServices = $this->getAssignmentPublicationServices();
            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                $assignmentPublicationService->deleteContentObjectPublicationsByObjectId($contentObject->getId());
            }
        }
        catch (Exception)
        {
            return false;
        }

        return true;
    }

    public function getAssignmentPublicationService(): AssignmentPublicationService
    {
        return $this->assignmentPublicationService;
    }

    /**
     * @return AssignmentPublicationService[]
     */
    protected function getAssignmentPublicationServices(): array
    {
        return [
            $this->getAssignmentPublicationService(),
            $this->getLearningPathAssignmentPublicationService()
        ];
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    public function getAssignmentService(): AssignmentService
    {
        return $this->assignmentService;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return  \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, string $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $publicationAttributes = DataManager::getContentObjectPublicationsAttributes(
            $objectIdentifier, $type, $condition, $count, $offset, $orderBy
        );

        $assignmentPublicationServices = $this->getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            $publicationAttributes = match ($type)
            {
                self::ATTRIBUTES_TYPE_USER => array_merge(
                    $publicationAttributes,
                    $assignmentPublicationService->getContentObjectPublicationAttributesForUser($objectIdentifier)
                ),
                default => array_merge(
                    $publicationAttributes,
                    $assignmentPublicationService->getContentObjectPublicationAttributesForContentObject(
                        $objectIdentifier
                    )
                ),
            };
        }

        return new ArrayCollection($publicationAttributes);
    }

    public function getLearningPathAssignmentPublicationService(): AssignmentPublicationService
    {
        return $this->learningPathAssignmentPublicationService;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
     */
    public function getLearningPathAssignmentService(): LearningPathAssignmentService
    {
        return $this->learningPathAssignmentService;
    }

    public function getPublicationTargetRenderer(): PublicationTargetRenderer
    {
        return $this->publicationTargetRenderer;
    }

    public function getPublicationTargetService(): PublicationTargetService
    {
        return $this->publicationTargetService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function isContentObjectPublished(string $contentObjectIdentifier): bool
    {
        if (DataManager::isContentObjectPublished($contentObjectIdentifier))
        {
            return true;
        }

        $assignmentPublicationServices = $this->getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            if ($assignmentPublicationService->areContentObjectsPublished([$contentObjectIdentifier]))
            {
                return true;
            }
        }

        return false;
    }

    public function setPublicationTargetRenderer(PublicationTargetRenderer $publicationTargetRenderer): void
    {
        $this->publicationTargetRenderer = $publicationTargetRenderer;
    }

    public function setPublicationTargetService(PublicationTargetService $publicationTargetService): void
    {
        $this->publicationTargetService = $publicationTargetService;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}