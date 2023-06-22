<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\LearningPath;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\File\Redirect;


/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\LearningPath
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathServiceBridge implements LearningPathServiceBridgeInterface
{
    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * @var CourseSettingsController
     */
    protected $courseSettingsController;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var Course
     */
    protected $course;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * @param LearningPathStepContextService $learningPathStepContextService
     * @param CourseService $courseService
     * @param CourseSettingsController $courseSettingsController
     */
    public function __construct(LearningPathStepContextService $learningPathStepContextService, CourseService $courseService, CourseSettingsController $courseSettingsController)
    {
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->courseService = $courseService;
        $this->courseSettingsController = $courseSettingsController;
    }

    /**
     * @param ContentObjectPublication $publication
     */
    public function setContentObjectPublication(ContentObjectPublication $publication)
    {
        $this->contentObjectPublication = $publication;
    }

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * @param string $toolName
     *
     * @return bool
     */
    public function isCourseToolActive(string $toolName): bool
    {
        if (empty($this->course))
        {
            return false;
        }
        $toolRegistrationId = $this->courseService->getToolRegistration($toolName)->getId();
        return $this->courseSettingsController->get_course_setting(
            $this->course,
            CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
            $toolRegistrationId);
    }

    /**
     * @return array
     */
    public function getCourseURLParameters(): array
    {
        if (empty($this->course))
        {
            return [];
        }

        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $parameters[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->course->getId();

        return $parameters;
    }

    /**
     * @return string
     */
    public function getCourseUrl(): string
    {
        if (empty($this->course))
        {
            return '';
        }

        $redirect = new Redirect($this->getCourseURLParameters());

        return $redirect->getUrl();
    }

    /**
     * @param string $toolName
     *
     * @return string
     */
    public function getCourseToolUrl(string $toolName): string
    {
        if (empty($this->course))
        {
            return '';
        }

        $parameters = $this->getCourseURLParameters();
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $toolName;

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     * @param Course $course
     *
     * @return LearningPathServiceBridge
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        $stepId = $this->treeNode->getId();
        $publicationClass = ContentObjectPublication::class_name();
        $publicationId = $this->contentObjectPublication->getId();
        $learningPathStepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($stepId, $publicationClass, $publicationId);
        return new ContextIdentifier(get_class($learningPathStepContext), $learningPathStepContext->getId());
    }

    /**
     * @return string
     */
    public function getContextTitle(): string
    {
        return $this->course instanceof Course ? $this->course->get_title() : '';
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        $this->treeNode = $treeNode;
        //$this->treeNodeConfiguration = $this->treeNode->getConfiguration(new EvaluationConfiguration());
    }

    /**
     * @param TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

}