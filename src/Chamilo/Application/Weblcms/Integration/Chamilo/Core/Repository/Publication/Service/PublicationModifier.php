<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Application\Weblcms\Manager as WeblcmsManager;
use Chamilo\Application\Weblcms\Service\ContentObjectPublicationMailer;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Tool\Manager as WeblcmsToolManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{
    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    private $courseService;

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     */
    public function __construct(Translator $translator, UserService $userService, CourseService $courseService)
    {
        $this->translator = $translator;
        $this->userService = $userService;
        $this->courseService = $courseService;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @see PublicationInterface::add_publication_attributes_elements()
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
        return Manager::add_publication_attributes_elements($formValidator);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication(int $publicationIdentifier)
    {
        return Manager::delete_content_object_publication($publicationIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier)
    {
        return Manager::get_content_object_publication_attribute($publicationIdentifier);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\CourseService
     */
    public function getCourseService(): CourseService
    {
        return $this->courseService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     */
    public function setCourseService(CourseService $courseService): void
    {
        $this->courseService = $courseService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult
     * @throws \Exception
     * @see PublicationInterface::publish_content_object()
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, $options = array()
    )
    {
        $course = $this->getCourseService()->getCourseById($publicationTarget->getCourseIdentifier());
        $toolNamespace = WeblcmsToolManager::get_tool_type_namespace($publicationTarget->getToolIdentifier());
        $toolName = $this->getTranslator()->trans('TypeName', [], $toolNamespace);

        $publication = new ContentObjectPublication();
        $publication->set_content_object_id($contentObject->getId());
        $publication->set_course_id($publicationTarget->getCourseIdentifier());
        $publication->set_tool($publicationTarget->getToolIdentifier());
        $publication->set_publisher_id($publicationTarget->getUserIdentifier());
        $publication->set_publication_date(time());
        $publication->set_modified_date(time());

        $isHidden = $options[ContentObjectPublication::PROPERTY_HIDDEN] ? 1 : 0;
        $publication->set_hidden($isHidden);

        $allowCollaboration = $options[ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] ? 1 : 0;
        $publication->set_allow_collaboration($allowCollaboration);

        if ($options['forever'] == 0)
        {
            $publication->set_from_date(DatetimeUtilities::time_from_datepicker($options['from_date']));
            $publication->set_to_date(DatetimeUtilities::time_from_datepicker($options['to_date']));
        }

        if (!$publication->create())
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', [
                '%CONTENT_OBJECT%' => $contentObject->get_title(), '%COURSE%' => $course->get_title(),
                '%TOOL%' => $toolName
            ], 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository'
            );

            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }

        $possible_publication_class =
            'Chamilo\Application\Weblcms\Tool\Implementation\\' . $publicationTarget->getToolIdentifier() .
            '\Storage\DataClass\Publication';
        if (class_exists($possible_publication_class))
        {
            $publication_extension = new $possible_publication_class();
            $publication_extension->set_publication_id($publication->getId());

            if (!$publication_extension->create())
            {
                $failureMessage = $this->getTranslator()->trans(
                    'PublicationFailure', [
                    '%CONTENT_OBJECT%' => $contentObject->get_title(), '%COURSE%' => $course->get_title(),
                    '%TOOL%' => $toolName
                ], 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository'
                );

                return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
            }
        }

        /**
         * @todo This part needs cleaning-up
         */
        if ($options[ContentObjectPublication::PROPERTY_EMAIL_SENT])
        {
            $mailerFactory = new MailerFactory(Configuration::getInstance());

            $contentObjectPublicationMailer = new ContentObjectPublicationMailer(
                $mailerFactory->getActiveMailer(), Translation::getInstance(), new CourseRepository(),
                new PublicationRepository(), new ContentObjectRepository(), $this->getUserService()
            );

            $contentObjectPublicationMailer->mailPublication($publication);
        }

        $successMessage = $this->getTranslator()->trans(
            'PublicationSuccess', [
            '%CONTENT_OBJECT%' => $contentObject->get_title(), '%COURSE%' => $course->get_title(),
            '%COURSE_CODE%' => $course->get_visual_code(), '%TOOL%' => $toolName
        ], 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository'
        );

        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = WeblcmsManager::context();
        $parameters[WeblcmsManager::PARAM_ACTION] = WeblcmsManager::ACTION_VIEW_COURSE;
        $parameters[WeblcmsManager::PARAM_COURSE] = $publicationTarget->getCourseIdentifier();
        $parameters[WeblcmsManager::PARAM_TOOL] = $publicationTarget->getToolIdentifier();

        if (!$contentObject instanceof Introduction)
        {
            $parameters[WeblcmsToolManager::PARAM_ACTION] = WeblcmsToolManager::ACTION_VIEW;
            $parameters[WeblcmsToolManager::PARAM_PUBLICATION_ID] = $publication->get_id();
        }

        $publicationUrl = new Redirect($parameters);

        return new PublicationResult(
            PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl->getUrl()
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $publicationAttributes
     *
     * @return boolean
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes)
    {
        return Manager::update_content_object_publication_id($publicationAttributes);
    }
}