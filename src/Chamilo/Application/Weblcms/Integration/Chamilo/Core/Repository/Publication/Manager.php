<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service\AssignmentPublicationService;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Service\ContentObjectPublicationMailer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class Manager implements PublicationInterface
{
    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::is_content_object_editable()
     */
    public static function is_content_object_editable($object_id)
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        /** @var AssignmentService $assignmentService */
        $assignmentService =
            $container->get(AssignmentService::class);

        /** @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService $learningPathAssignmentService */
        $learningPathAssignmentService = $container->get(
            \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService::class
        );

        $contentObject = new ContentObject();
        $contentObject->setId($object_id);

        if ($assignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        if ($learningPathAssignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::content_object_is_published()
     */
    public static function content_object_is_published($object_id)
    {
        if (DataManager::content_object_is_published($object_id))
        {
            return true;
        }

        $assignmentPublicationServices = self::getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            if ($assignmentPublicationService->areContentObjectsPublished([$object_id]))
            {
                return true;
            }
        }

        return false;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::any_content_object_is_published()
     */
    public static function any_content_object_is_published($object_ids)
    {
        if (DataManager::any_content_object_is_published($object_ids))
        {
            return true;
        }

        $assignmentPublicationServices = self::getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            if ($assignmentPublicationService->areContentObjectsPublished($object_ids))
            {
                return true;
            }
        }

        return false;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attributes()
     */
    public static function get_content_object_publication_attributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
        $offset = null, $order_properties = null
    )
    {
        $publicationAttributes = DataManager::get_content_object_publication_attributes(
            $object_id,
            $type,
            $condition,
            $count,
            $offset,
            $order_properties
        );

        $assignmentPublicationServices = self::getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            switch ($type)
            {
                case self::ATTRIBUTES_TYPE_OBJECT:
                default:
                    $publicationAttributes = array_merge(
                        $publicationAttributes,
                        $assignmentPublicationService->getContentObjectPublicationAttributesForContentObject($object_id)
                    );
                case self::ATTRIBUTES_TYPE_USER:
                    $publicationAttributes = array_merge(
                        $publicationAttributes,
                        $assignmentPublicationService->getContentObjectPublicationAttributesForUser($object_id)
                    );
            }
        }

        return $publicationAttributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */
    public static function get_content_object_publication_attribute($publication_id, $context = null)
    {
        if (!empty($context) || $context == ContentObjectPublication::class)
        {
            return DataManager::get_content_object_publication_attribute($publication_id);
        }

        $assignmentPublicationServices = self::getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            if ($assignmentPublicationService->getPublicationContext() == $context)
            {
                return $assignmentPublicationService->getContentObjectPublicationAttributes($publication_id);
            }
        }

        return null;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::count_publication_attributes()
     */
    public static function count_publication_attributes($attributes_type = null, $identifier = null, $condition = null)
    {
        $count = DataManager::count_publication_attributes($attributes_type, $identifier, $condition);

        $assignmentPublicationServices = self::getAssignmentPublicationServices();
        foreach ($assignmentPublicationServices as $assignmentPublicationService)
        {
            switch ($attributes_type)
            {
                case self::ATTRIBUTES_TYPE_OBJECT:
                default:
                    $count += $assignmentPublicationService->countContentObjectPublicationAttributesForContentObject(
                        $identifier
                    );
                case self::ATTRIBUTES_TYPE_USER:
                    $count += $assignmentPublicationService->countContentObjectPublicationAttributesForContentObject(
                        $identifier
                    );
            }
        }

        return $count;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publications()
     */
    public static function delete_content_object_publications($object_id)
    {
        if (!DataManager::delete_content_object_publications($object_id))
        {
            return false;
        }

        try
        {
            $assignmentPublicationServices = self::getAssignmentPublicationServices();
            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                $assignmentPublicationService->deleteContentObjectPublicationsByObjectId($object_id);
            }
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */
    public static function delete_content_object_publication($publication_id, $context = null)
    {
        if (empty($context) || $context == ContentObjectPublication::class)
        {
            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $publication_id);
            if (!$publication)
            {
                return false;
            }

            return $publication->delete();
        }

        try
        {
            $assignmentPublicationServices = self::getAssignmentPublicationServices();
            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                if ($assignmentPublicationService->getPublicationContext() == $context)
                {
                    $assignmentPublicationService->deleteContentObjectPublicationsByPublicationId($publication_id);

                    break;
                }
            }

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_locations()
     */
    public static function get_content_object_publication_locations($content_object, $user = null)
    {
        $locations = new Locations(__NAMESPACE__);
        $type = $content_object->get_type();

        $excludedCourseTypeSetting = (string) Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'excluded_course_types')
        );

        if (!empty($excludedCourseTypeSetting))
        {
            $excludedCourseTypes = explode(',', $excludedCourseTypeSetting);

            $condition = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_COURSE_TYPE_ID),
                    $excludedCourseTypes
                )
            );
        }

        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_courses_from_user(
            $user,
            $condition
        );

        $possible_courses = array();

        while ($course = $courses->next_result())
        {
            if ($course->is_course_admin($user))
            {
                $possible_courses[] = $course;
            }
        }

        $course_settings_controller = CourseSettingsController::getInstance();
        $course_management_rights = CourseManagementRights::getInstance();

        $tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters());

        $tool_names = array();

        while ($tool = $tools->next_result())
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
                    $class,
                    'Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface'
                ))
                {
                    $types[$tool->get_id()][] = Introduction::class_name();
                    $tool_names[$tool->get_id()] = $tool->get_name();
                }
            }
        }

        foreach ($types as $tool_id => $allowed_types)
        {
            if (in_array($type, $allowed_types))
            {
                foreach ($possible_courses as $course)
                {
                    if ($type == Introduction::class_name() && (!$course_settings_controller->get_course_setting(
                                $course,
                                CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
                            ) || !empty(
                            \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_introduction_publication_by_course_and_tool(
                                $course->getId(),
                                $tool_names[$tool_id]
                            )
                            ))
                    )
                    {
                        continue;
                    }

                    if ($course_settings_controller->get_course_setting(
                            $course,
                            CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
                            $tool_id
                        ) && $course_management_rights->is_allowed(
                            CourseManagementRights::PUBLISH_FROM_REPOSITORY_RIGHT,
                            $course->get_id()
                        )
                    )
                    {
                        $tool_namespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace(
                            $tool_names[$tool_id]
                        );
                        $tool_name = Translation::get('TypeName', null, $tool_namespace);

                        $locations->add_location(
                            new Location(
                                $course->get_id(),
                                $tool_names[$tool_id],
                                $course->get_title(),
                                $course->get_visual_code(),
                                $tool_name
                            )
                        );
                    }
                }
            }
        }

        return $locations;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::publish_content_object()
     */
    public static function publish_content_object(
        ContentObject $content_object, LocationSupport $location,
        $options = array()
    )
    {
        $publication = new ContentObjectPublication();
        $publication->set_content_object_id($content_object->get_id());
        $publication->set_course_id($location->get_course_id());
        $publication->set_tool($location->get_tool_id());
        $publication->set_publisher_id(Session::get_user_id());
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
            return false;
        }

        $possible_publication_class = 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $location->get_tool_id() .
            '\Storage\DataClass\Publication';
        if (class_exists($possible_publication_class))
        {
            $publication_extension = new $possible_publication_class();
            $publication_extension->set_publication_id($publication->get_id());

            if (!$publication_extension->create())
            {
                return false;
            }
        }

        if ($options[ContentObjectPublication::PROPERTY_EMAIL_SENT])
        {
            $mailerFactory = new MailerFactory(Configuration::getInstance());

            $contentObjectPublicationMailer = new ContentObjectPublicationMailer(
                $mailerFactory->getActiveMailer(),
                Translation::getInstance(),
                new CourseRepository(),
                new PublicationRepository(),
                new ContentObjectRepository(),
                new UserRepository()
            );

            $contentObjectPublicationMailer->mailPublication($publication);
        }

        return $publication;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */
    public static function add_publication_attributes_elements($form)
    {
        $registration = Configuration::registration(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__)
        );

        $splitterHtml = array();

        $splitterHtml[] = '<div class="form_splitter" >';
        $splitterHtml[] = '<span class="category">' .
            Translation::get('PublicationDetails', null, \Chamilo\Application\Weblcms\Manager::context()) . '</span>';
        $splitterHtml[] = '<div style="clear: both;"></div>';
        $splitterHtml[] = '</div>';

        $form->addElement('html', implode(PHP_EOL, $splitterHtml));

        $form->addElement(
            'checkbox',
            \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] .
            '][' . ContentObjectPublication::PROPERTY_HIDDEN . ']',
            Translation::get('Hidden', null, \Chamilo\Application\Weblcms\Manager::context())
        );
        $form->add_forever_or_timewindow(
            'PublicationPeriod',
            \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] .
            ']',
            true
        );
        $form->addElement(
            'checkbox',
            \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] .
            '][' . ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION . ']',
            Translation::get('CourseAdminCollaborate', null, \Chamilo\Application\Weblcms\Manager::context())
        );

        $form->addElement(
            'checkbox',
            \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] .
            '][' . ContentObjectPublication::PROPERTY_EMAIL_SENT . ']',
            Translation::get('SendByEMail', null, \Chamilo\Application\Weblcms\Manager::context())
        );

        $defaults[\Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION][$registration[Registration::PROPERTY_ID]]['forever'] =
            1;

        $defaults[\Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION][$registration[Registration::PROPERTY_ID]][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $form->setDefaults($defaults);
    }

    /**
     * @param Attributes $publication_attributes
     *
     * @return bool
     */
    public static function update_content_object_publication_id($publication_attributes)
    {
        $context = $publication_attributes->getPublicationContext();
        if (empty($context) || $context == ContentObjectPublication::class)
        {
            return DataManager::update_content_object_publication_id($publication_attributes);
        }

        try
        {
            $assignmentPublicationServices = self::getAssignmentPublicationServices();
            foreach ($assignmentPublicationServices as $assignmentPublicationService)
            {
                if ($assignmentPublicationService->getPublicationContext() == $context)
                {
                    $assignmentPublicationService->updateContentObjectId(
                        $publication_attributes->getId(), $publication_attributes->get_content_object_id()
                    );

                    break;
                }
            }

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * @return AssignmentPublicationService[]
     */
    protected static function getAssignmentPublicationServices()
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        return [
            $container->get(
                'chamilo.application.weblcms.integration.chamilo.core.repository.publication.service.content_object_publication_manager.assignment_publication_service'
            ),
            $container->get(
                'chamilo.application.weblcms.integration.chamilo.core.repository.publication.service.content_object_publication_manager.learning_path_assignment_publication_service'
            ),
        ];
    }
}
