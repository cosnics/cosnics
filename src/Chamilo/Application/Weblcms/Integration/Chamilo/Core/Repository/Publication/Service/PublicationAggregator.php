<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Configuration;
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
    protected AssignmentService $assignmentService;

    protected LearningPathAssignmentService $learningPathAssignmentService;

    protected UserService $userService;

    private PublicationTargetRenderer $publicationTargetRenderer;

    private PublicationTargetService $publicationTargetService;

    private Translator $translator;

    public function __construct(
        AssignmentService $assignmentService, LearningPathAssignmentService $learningPathAssignmentService,
        UserService $userService, Translator $translator, PublicationTargetService $publicationTargetService,
        PublicationTargetRenderer $publicationTargetRenderer
    )
    {
        $this->assignmentService = $assignmentService;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->publicationTargetService = $publicationTargetService;
        $this->publicationTargetRenderer = $publicationTargetRenderer;
    }

    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    )
    {
        $type = $contentObject->getType();

        $excludedCourseTypeSetting = (string) Configuration::getInstance()->get_setting(
            ['Chamilo\Application\Weblcms', 'excluded_course_types']
        );

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

        $tool_names = [];

        foreach ($tools as $tool)
        {
            $tool_name = $tool->get_name();

            $class = $tool->getContext() . '\Manager';

            if (class_exists($class))
            {
                $allowed_types = $class::get_allowed_types();

                if (count($allowed_types) > 0)
                {
                    $types[$tool->get_id()] = $allowed_types;
                    $tool_names[$tool->get_id()] = $tool->get_name();
                }

                if (is_subclass_of(
                    $class, 'Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface'
                ))
                {
                    $types[$tool->get_id()][] = Introduction::class;
                    $tool_names[$tool->get_id()] = $tool->get_name();
                }
            }
        }

        $columnNames = [];
        $columnNames[] = $this->getTranslator()->trans('Course', [], \Chamilo\Application\Weblcms\Manager::context());
        $columnNames[] = $this->getTranslator()->trans('Tool', [], \Chamilo\Application\Weblcms\Manager::context());

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
                                $course->getId(), $tool_names[$tool_id]
                            )
                            )))
                    {
                        continue;
                    }

                    if ($course_settings_controller->get_course_setting(
                            $course, CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $tool_id
                        ) && $course_management_rights->is_allowed_management(
                            CourseManagementRights::PUBLISH_FROM_REPOSITORY_RIGHT, $course->get_id()
                        ))
                    {
                        $tool_namespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace(
                            $tool_names[$tool_id]
                        );
                        $tool_name = $this->getTranslator()->trans('TypeName', [], $tool_namespace);

                        $publicationTargetKey = $this->getPublicationTargetService()->addPublicationTargetAndGetKey(
                            new PublicationTarget(
                                PublicationModifier::class, $course->get_id(), $tool_names[$tool_id], $user->getId()
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
        return Manager::areContentObjectsPublished($contentObjectIdentifiers);
    }

    public function canContentObjectBeEdited(int $contentObjectIdentifier): bool
    {
        return Manager::canContentObjectBeEdited($contentObjectIdentifier);
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        $user = $this->userService->findUserByIdentifier((string) $contentObject->get_owner_id());
        $isTeacher = ($user instanceof User && $user->get_status() == User::STATUS_TEACHER);

        if ($this->assignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher && !$this->assignmentService->isContentObjectOwnerSameAsSubmitter($contentObject));
        }

        if ($this->learningPathAssignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher &&
                !$this->learningPathAssignmentService->isContentObjectOwnerSameAsSubmitter($contentObject));
        }

        return true;
    }

    public function countPublicationAttributes(int $type, int $objectIdentifier, ?Condition $condition = null): int
    {
        return Manager::countPublicationAttributes($type, $objectIdentifier, $condition);
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        return Manager::deleteContentObjectPublications($contentObject->getId());
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return  \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return Manager::getContentObjectPublicationsAttributes(
            $objectIdentifier, $type, $condition, $count, $offset, $orderBy
        );
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

    public function isContentObjectPublished(int $contentObjectIdentifier): bool
    {
        return Manager::isContentObjectPublished($contentObjectIdentifier);
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