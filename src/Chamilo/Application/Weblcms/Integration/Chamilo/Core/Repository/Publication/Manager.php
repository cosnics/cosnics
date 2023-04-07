<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service\AssignmentPublicationService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class Manager implements PublicationInterface
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public static function add_publication_attributes_elements($form)
    {
        $registration = Configuration::registration(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__)
        );

        $form->addElement(
            'html', '<h5>' . Translation::get('PublicationDetails', null, 'Chamilo\Application\Weblcms') . '</h5>'
        );

        $form->addElement(
            'checkbox', \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] . '][' . ContentObjectPublication::PROPERTY_HIDDEN . ']',
            Translation::get('Hidden', null, \Chamilo\Application\Weblcms\Manager::context())
        );

        $form->addTimePeriodSelection(
            'PublicationPeriod', ContentObjectPublication::PROPERTY_FROM_DATE,
            ContentObjectPublication::PROPERTY_TO_DATE, FormValidator::PROPERTY_TIME_PERIOD_FOREVER,
            \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] . ']'
        );

        $form->addElement(
            'checkbox', \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] . '][' . ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION .
            ']', Translation::get('CourseAdminCollaborate', null, \Chamilo\Application\Weblcms\Manager::context())
        );

        $form->addElement(
            'checkbox', \Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION . '[' .
            $registration[Registration::PROPERTY_ID] . '][' . ContentObjectPublication::PROPERTY_EMAIL_SENT . ']',
            Translation::get('SendByEMail', null, \Chamilo\Application\Weblcms\Manager::context())
        );

        $defaults[\Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION][$registration[Registration::PROPERTY_ID]][FormValidator::PROPERTY_TIME_PERIOD_FOREVER] =
            1;

        $defaults[\Chamilo\Core\Repository\Publication\Manager::WIZARD_OPTION][$registration[Registration::PROPERTY_ID]][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $form->setDefaults($defaults);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::isContentObjectPublished()
     */

    public static function areContentObjectsPublished($object_ids)
    {
        if (DataManager::areContentObjectsPublished($object_ids))
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
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::areContentObjectsPublished()
     */

    public static function canContentObjectBeEdited($object_id)
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        /** @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService */
        $assignmentService = $container->get(AssignmentService::class);

        /** @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService */
        $learningPathAssignmentService = $container->get(
            LearningPathAssignmentService::class
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
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationsAttributes()
     */

    public static function countPublicationAttributes($attributes_type = null, $identifier = null, $condition = null)
    {
        $count = DataManager::countPublicationAttributes($attributes_type, $identifier, $condition);

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
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */

    public static function deleteContentObjectPublications($object_id)
    {
        if (!DataManager::deleteContentObjectPublications($object_id))
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
        catch (Exception $ex)
        {
            return false;
        }

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::countPublicationAttributes()
     */

    public static function delete_content_object_publication($publication_id, $context = null)
    {
        if (empty($context) || $context == ContentObjectPublication::class)
        {
            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class, $publication_id);
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
        catch (Exception $ex)
        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::deleteContentObjectPublications()
     */

    /**
     * @return AssignmentPublicationService[]
     */
    protected static function getAssignmentPublicationServices()
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        return [
            $container->get(
                'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAggregator\AssignmentPublicationService'
            ),
            $container->get(
                'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAggregator\LearningPathAssignmentPublicationService'
            ),
        ];
    }

    public static function getContentObjectPublicationsAttributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, $offset = null,
        $order_properties = null
    )
    {
        $publicationAttributes = DataManager::getContentObjectPublicationsAttributes(
            $object_id, $type, $condition, $count, $offset, $order_properties
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

        return new ArrayCollection($publicationAttributes);
    }

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
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */

    public static function isContentObjectPublished($object_id)
    {
        if (DataManager::isContentObjectPublished($object_id))
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
        catch (Exception $ex)
        {
            return false;
        }
    }
}
