<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Path;

/**
 * $Id: learning_path.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.learning_path
 */
class LearningPath extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_AUTOMATIC_NUMBERING = 'automatic_numbering';
    const PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'enforce_default_traversing_order';

    const AUTOMATIC_NUMBERING_NONE = 'none';
    const AUTOMATIC_NUMBERING_DIGITS = 'digits';

    // Currently not implemented options
    //const AUTOMATIC_NUMBERING_ALPHABETICAL = 'alphabetical';
    //const AUTOMATIC_NUMBERING_MIX = 'mix';

    /**
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_AUTOMATIC_NUMBERING, self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     * Returns the automatic numbering
     *
     * @return string
     */
    public function getAutomaticNumbering()
    {
        return $this->get_additional_property(self::PROPERTY_AUTOMATIC_NUMBERING);
    }

    /**
     * Returns whether or not the automatic numbering is activated for this learning path
     *
     * @return bool
     */
    public function usesAutomaticNumbering()
    {
        return $this->getAutomaticNumbering() != self::AUTOMATIC_NUMBERING_NONE;
    }

    /**
     * Sets the automatic numbering
     *
     * @param $automaticNumberingOption
     */
    public function setAutomaticNumbering($automaticNumberingOption)
    {
        if (!in_array($automaticNumberingOption, self::getAutomaticNumberingOptions()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given automaticNumberingOption must be one of %s',
                    explode(',', self::getAutomaticNumberingOptions())
                )
            );
        }

        $this->set_additional_property(self::PROPERTY_AUTOMATIC_NUMBERING, $automaticNumberingOption);
    }

    /**
     * Returns a list of automatic numbering options
     *
     * @return string[]
     */
    public static function getAutomaticNumberingOptions()
    {
        return array(
            self::AUTOMATIC_NUMBERING_NONE, self::AUTOMATIC_NUMBERING_DIGITS
        );
    }

    /**
     * Sets whether or not the default traversing order should be enforced
     *
     * @param bool $enforceDefaultTraversingOrder
     */
    public function setEnforceDefaultTraversingOrder($enforceDefaultTraversingOrder = true)
    {
        if(!is_bool($enforceDefaultTraversingOrder))
        {
            throw new \InvalidArgumentException('The given enforceDefaultTraversingOrder is no valid boolean');
        }

        $this->set_additional_property(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER, $enforceDefaultTraversingOrder);
    }

    /**
     * Returns whether or not the default traversing order is enforced
     *
     * @return bool
     */
    public function enforcesDefaultTraversingOrder()
    {
        return (bool) $this->get_additional_property(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     * Delete a Forum Topic and all its posts.
     *
     * @return boolean Returns whether the delete was succesfull.
     */
    public function delete($only_version)
    {
        if ($only_version)
        {
            $this->getLearningPathChildService()->emptyLearningPath($this);
        }

        return parent::delete($only_version);
    }

    /**
     * @return object | LearningPathChildService
     */
    protected function getLearningPathChildService()
    {
        $serviceContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $serviceContainer->get(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_child_service'
        );
    }

    /**
     * @return array
     */
    public function get_allowed_types()
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $configuration = Configuration::getInstance();

        $registrations = $configuration->getIntegrationRegistrations(self::package());
        $types = array();

        foreach ($registrations as $registration)
        {
            $type = $registration[Registration::PROPERTY_TYPE];
            $parentContext = $classNameUtilities->getNamespaceParent($type);
            $parentRegistration = $configuration->get_registration($parentContext);

            if ($parentRegistration[Registration::PROPERTY_TYPE] ==
                \Chamilo\Core\Repository\Manager::context() . '\ContentObject'
            )
            {
                $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                    $registration[Registration::PROPERTY_CONTEXT],
                    6
                );
                $types[] = $namespace . '\Storage\DataClass\\' .
                    ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            }
        }

        return $types;
    }
}
