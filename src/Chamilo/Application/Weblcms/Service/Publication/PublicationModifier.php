<?php
namespace Chamilo\Application\Weblcms\Service\Publication;

use Chamilo\Application\Weblcms\Manager as WeblcmsManager;
use Chamilo\Application\Weblcms\Service\ContentObjectPublicationMailer;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Tool\Manager as WeblcmsToolManager;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    protected MailerInterface $activeMailer;

    protected AssignmentPublicationService $assignmentPublicationService;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected DatetimeUtilities $datetimeUtilities;

    protected AssignmentPublicationService $learningPathAssignmentPublicationService;

    protected RegistrationConsulter $registrationConsulter;

    protected UrlGenerator $urlGenerator;

    private CourseService $courseService;

    private ThemePathBuilder $themeWebPathBuilder;

    private Translator $translator;

    private UserService $userService;

    public function __construct(
        Translator $translator, UserService $userService, CourseService $courseService,
        ThemePathBuilder $themeWebPathBuilder, UrlGenerator $urlGenerator, DatetimeUtilities $datetimeUtilities,
        RegistrationConsulter $registrationConsulter, AssignmentPublicationService $assignmentPublicationService,
        AssignmentPublicationService $learningPathAssignmentPublicationService,
        ConfigurablePathBuilder $configurablePathBuilder, MailerInterface $activeMailer
    )
    {
        $this->translator = $translator;
        $this->userService = $userService;
        $this->courseService = $courseService;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->urlGenerator = $urlGenerator;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->registrationConsulter = $registrationConsulter;
        $this->assignmentPublicationService = $assignmentPublicationService;
        $this->learningPathAssignmentPublicationService = $learningPathAssignmentPublicationService;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->activeMailer = $activeMailer;
    }

    /**
     * @throws \QuickformException
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
        $registration = $this->getRegistrationConsulter()->getRegistrationForContext(
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication'
        );
        $translator = $this->getTranslator();

        $formValidator->addElement(
            'html', '<h5>' . $translator->trans('PublicationDetails', [], 'Chamilo\Application\Weblcms') . '</h5>'
        );

        $formValidator->addElement(
            'checkbox', Manager::WIZARD_OPTION . '[' . $registration[DataClass::PROPERTY_ID] . '][' .
            ContentObjectPublication::PROPERTY_HIDDEN . ']', $translator->trans('Hidden', [], WeblcmsManager::CONTEXT)
        );

        $formValidator->addTimePeriodSelection(
            'PublicationPeriod', ContentObjectPublication::PROPERTY_FROM_DATE,
            ContentObjectPublication::PROPERTY_TO_DATE, FormValidator::PROPERTY_TIME_PERIOD_FOREVER,
            Manager::WIZARD_OPTION . '[' . $registration[DataClass::PROPERTY_ID] . ']'
        );

        $formValidator->addElement(
            'checkbox', Manager::WIZARD_OPTION . '[' . $registration[DataClass::PROPERTY_ID] . '][' .
            ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION . ']',
            $translator->trans('CourseAdminCollaborate', [], WeblcmsManager::CONTEXT)
        );

        $formValidator->addElement(
            'checkbox', Manager::WIZARD_OPTION . '[' . $registration[DataClass::PROPERTY_ID] . '][' .
            ContentObjectPublication::PROPERTY_EMAIL_SENT . ']',
            $translator->trans('SendByEMail', [], WeblcmsManager::CONTEXT)
        );

        $defaults[Manager::WIZARD_OPTION][$registration[DataClass::PROPERTY_ID]][FormValidator::PROPERTY_TIME_PERIOD_FOREVER] =
            1;

        $defaults[Manager::WIZARD_OPTION][$registration[DataClass::PROPERTY_ID]][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $formValidator->setDefaults($defaults);
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function deleteContentObjectPublication(int $publicationIdentifier): bool
    {
        if (empty($context) || $context == ContentObjectPublication::class)
        {
            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class, $publicationIdentifier);
            if (!$publication)
            {
                return false;
            }

            return $publication->delete();
        }

        try
        {
            $assignmentPublicationServices = $this->getAssignmentPublicationServices();

            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                if ($assignmentPublicationService->getPublicationContext() == $context)
                {
                    $assignmentPublicationService->deleteContentObjectPublicationsByPublicationId(
                        $publicationIdentifier
                    );

                    break;
                }
            }

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function getActiveMailer(): MailerInterface
    {
        return $this->activeMailer;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\Publication\AssignmentPublicationService
     */
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

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes
    {
        return DataManager::get_content_object_publication_attribute($publicationIdentifier);

        // TODO: This should be solved differently, should be seperate implementations
        //        $assignmentPublicationServices = $this->getAssignmentPublicationServices();
        //
        //        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        //        {
        //            if ($assignmentPublicationService->getPublicationContext() == $context)
        //            {
        //                return $assignmentPublicationService->getContentObjectPublicationAttributes($publicationIdentifier);
        //            }
        //        }
    }

    public function getCourseService(): CourseService
    {
        return $this->courseService;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\Publication\AssignmentPublicationService
     */
    public function getLearningPathAssignmentPublicationService(): AssignmentPublicationService
    {
        return $this->learningPathAssignmentPublicationService;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @throws \Exception
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
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

        if ($options[FormValidator::PROPERTY_TIME_PERIOD_FOREVER] == 0)
        {
            $publication->set_from_date(
                $this->getDatetimeUtilities()->timeFromDatepicker(
                    $options[ContentObjectPublication::PROPERTY_FROM_DATE]
                )
            );
            $publication->set_to_date(
                $this->getDatetimeUtilities()->timeFromDatepicker(
                    $options[ContentObjectPublication::PROPERTY_TO_DATE]
                )
            );
        }

        if (!$publication->create())
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', [
                '%CONTENT_OBJECT%' => $contentObject->get_title(),
                '%COURSE%' => $course->get_title(),
                '%TOOL%' => $toolName
            ], 'Chamilo\Application\Weblcms'
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
                    '%CONTENT_OBJECT%' => $contentObject->get_title(),
                    '%COURSE%' => $course->get_title(),
                    '%TOOL%' => $toolName
                ], 'Chamilo\Application\Weblcms'
                );

                return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
            }
        }

        /**
         * @todo This part needs cleaning-up
         */
        if ($options[ContentObjectPublication::PROPERTY_EMAIL_SENT])
        {
            $contentObjectPublicationMailer = new ContentObjectPublicationMailer(
                $this->getActiveMailer(), $this->getTranslator(), new CourseRepository(), new PublicationRepository(),
                new ContentObjectRepository(), $this->getUserService(), $this->getThemeWebPathBuilder(),
                $this->getConfigurablePathBuilder()
            );

            $contentObjectPublicationMailer->mailPublication($publication);
        }

        $successMessage = $this->getTranslator()->trans(
            'PublicationSuccess', [
            '%CONTENT_OBJECT%' => $contentObject->get_title(),
            '%COURSE%' => $course->get_title(),
            '%COURSE_CODE%' => $course->get_visual_code(),
            '%TOOL%' => $toolName
        ], 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository'
        );

        $parameters = [];
        $parameters[Application::PARAM_CONTEXT] = WeblcmsManager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = WeblcmsManager::ACTION_VIEW_COURSE;
        $parameters[WeblcmsManager::PARAM_COURSE] = $publicationTarget->getCourseIdentifier();
        $parameters[WeblcmsManager::PARAM_TOOL] = $publicationTarget->getToolIdentifier();

        if (!$contentObject instanceof Introduction)
        {
            $parameters[WeblcmsToolManager::PARAM_ACTION] = WeblcmsToolManager::ACTION_VIEW;
            $parameters[WeblcmsToolManager::PARAM_PUBLICATION_ID] = $publication->getId();
        }

        $publicationUrl = $this->getUrlGenerator()->fromParameters($parameters);

        return new PublicationResult(
            PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl
        );
    }

    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool
    {
        $context = $publicationAttributes->getPublicationContext();

        if (empty($context) || $context == ContentObjectPublication::class)
        {
            return DataManager::update_content_object_publication_id($publicationAttributes);
        }

        try
        {
            $assignmentPublicationServices = $this->getAssignmentPublicationServices();

            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                if ($assignmentPublicationService->getPublicationContext() == $context)
                {
                    $assignmentPublicationService->updateContentObjectId(
                        $publicationAttributes->getId(), $publicationAttributes->get_content_object_id()
                    );

                    break;
                }
            }

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }
}