<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\ContentObject\LearningPath\Course\Storage\DataClass\Course;
use Chamilo\Core\Repository\ContentObject\LearningPath\CourseSettingsConnector;
use Chamilo\Core\Repository\ContentObject\LearningPath\CourseSettingsController;
use Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service\LearningPathPublicationService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Rights\CourseManagementRights;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ContentObjectPublicationMailer;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\CourseSetting;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\CourseTool;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CourseRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\PublicationRepository;
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
use Chamilo\Libraries\Platform\Translation;
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
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::content_object_is_published()
     */
    public static function content_object_is_published($object_id)
    {
        return self::getLearningPathPublicationService()->areContentObjectsPublished(array($object_id));
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::any_content_object_is_published()
     */
    public static function any_content_object_is_published($object_ids)
    {
        return self::getLearningPathPublicationService()->areContentObjectsPublished($object_ids);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attributes()
     */
    public static function get_content_object_publication_attributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
        $offset = null, $order_properties = null
    )
    {
        if ($type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return self::getLearningPathPublicationService()->getContentObjectPublicationAttributesForContentObject(
                $object_id, $condition, $count, $offset, $order_properties
            );
        }
        else
        {
            return self::getLearningPathPublicationService()->getContentObjectPublicationAttributesForUser(
                $object_id, $condition, $count, $offset, $order_properties
            );
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */
    public static function get_content_object_publication_attribute($publication_id)
    {
        return self::getLearningPathPublicationService()->getContentObjectPublicationAttributesForLearningPathChild(
            $publication_id
        );
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::count_publication_attributes()
     */
    public static function count_publication_attributes($attributes_type = null, $identifier = null, $condition = null)
    {
        if ($attributes_type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return self::getLearningPathPublicationService()->countContentObjectPublicationAttributesForContentObject(
                $identifier, $condition
            );
        }
        else
        {
            return self::getLearningPathPublicationService()->countContentObjectPublicationAttributesForUser(
                $identifier, $condition
            );
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publications()
     */
    public static function delete_content_object_publications($object_id)
    {
        try
        {
            self::getLearningPathPublicationService()->deleteContentObjectPublicationsByObjectId($object_id);

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */
    public static function delete_content_object_publication($publication_id)
    {
        try
        {
            self::getLearningPathPublicationService()->deleteContentObjectPublicationsByLearningPathChildId(
                $publication_id
            );

            return true;
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * @param Attributes $publication_attributes
     *
     * @return bool
     */
    public static function update_content_object_publication_id($publication_attributes)
    {
        try
        {
            self::getLearningPathPublicationService()->updateContentObjectIdInLearningPathChild(
                $publication_attributes->getId(), $publication_attributes->get_content_object_id()
            );

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
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */
    public static function add_publication_attributes_elements($form)
    {
    }

    /**
     * @return LearningPathPublicationService | object
     */
    protected static function getLearningPathPublicationService()
    {
        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $dependencyInjectionContainer->get(
            'chamilo.core.repository.content_object.learning_path.integration.' .
            'chamilo.core.repository.publication.service.learning_path_publication_service'
        );
    }
}
