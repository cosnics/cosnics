<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Session\Session;
use InvalidArgumentException;

/**
 *
 * @package repository.lib.content_object.learning_path
 */
class LearningPath extends ContentObject implements ComplexContentObjectSupport
{
    const AUTOMATIC_NUMBERING_DIGITS = 'digits';
    const AUTOMATIC_NUMBERING_NONE = 'none';

    const PROPERTY_AUTOMATIC_NUMBERING = 'automatic_numbering';
    const PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'enforce_default_traversing_order';

    // Currently not implemented options
    // const AUTOMATIC_NUMBERING_ALPHABETICAL = 'alphabetical';
    // const AUTOMATIC_NUMBERING_MIX = 'mix';

    /**
     * Creates the LearningPath and create a TreeNodeDataRecord
     *
     * @param bool $create_in_batch
     *
     * @return bool
     */
    public function create($create_in_batch = false)
    {
        if (!parent::create($create_in_batch))
        {
            return false;
        }

        $user = new User();
        $user->setId(Session::get_user_id());

        $this->getTreeNodeDataService()->createTreeNodeDataForLearningPath($this, $user);

        return true;
    }

    /**
     * Delete a LearningPath and all of it's node data
     *
     * @param bool $only_version
     *
     * @return boolean Returns whether the delete was succesfull.
     */
    public function delete($only_version = false)
    {
        if ($only_version)
        {
            $this->getLearningPathService()->emptyLearningPath($this);
        }

        return parent::delete($only_version);
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
     * Returns the automatic numbering
     *
     * @return string
     */
    public function getAutomaticNumbering()
    {
        return $this->get_additional_property(self::PROPERTY_AUTOMATIC_NUMBERING);
    }

    /**
     * Returns a list of automatic numbering options
     *
     * @return string[]
     */
    public static function getAutomaticNumberingOptions()
    {
        return array(self::AUTOMATIC_NUMBERING_NONE, self::AUTOMATIC_NUMBERING_DIGITS);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     * @throws \Exception
     */
    protected function getLearningPathService()
    {
        return $this->getService(LearningPathService::class);
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     * @throws \Exception
     */
    protected function getTreeNodeDataService()
    {
        return $this->getService(TreeNodeDataService::class);
    }

    /**
     *
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_AUTOMATIC_NUMBERING, self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     *
     * @return array
     */
    public function get_allowed_types()
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $configuration = Configuration::getInstance();

        $registrations = $configuration->getIntegrationRegistrations(self::package());
        $types = [];

        usort(
            $registrations, function ($registrationA, $registrationB) {
            return $registrationA[Registration::PROPERTY_PRIORITY] < $registrationB[Registration::PROPERTY_PRIORITY];
        }
        );

        foreach ($registrations as $registration)
        {
            $type = $registration[Registration::PROPERTY_TYPE];
            $parentContext = $classNameUtilities->getNamespaceParent($type);
            $parentRegistration = $configuration->get_registration($parentContext);

            if ($parentContext == 'Chamilo\Core\Repository\ContentObject\Section')
            {
                continue;
            }

            if ($parentRegistration[Registration::PROPERTY_TYPE] == Manager::context() . '\ContentObject')
            {
                $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                    $registration[Registration::PROPERTY_CONTEXT], 6
                );
                $types[] = $namespace . '\Storage\DataClass\\' .
                    ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            }
        }

        return $types;
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_learning_path';
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
            throw new InvalidArgumentException(
                sprintf(
                    'The given automaticNumberingOption must be one of %s',
                    explode(',', self::getAutomaticNumberingOptions())
                )
            );
        }

        $this->set_additional_property(self::PROPERTY_AUTOMATIC_NUMBERING, $automaticNumberingOption);
    }

    /**
     * Sets whether or not the default traversing order should be enforced
     *
     * @param bool $enforceDefaultTraversingOrder
     */
    public function setEnforceDefaultTraversingOrder($enforceDefaultTraversingOrder = true)
    {
        if (!is_bool($enforceDefaultTraversingOrder))
        {
            throw new InvalidArgumentException('The given enforceDefaultTraversingOrder is no valid boolean');
        }

        $this->set_additional_property(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER, $enforceDefaultTraversingOrder);
    }

    public function update($trueUpdate = true)
    {
        if (!parent::update($trueUpdate))
        {
            return false;
        }

        $this->getTreeNodeDataService()->updateTreeNodeDataForLearningPath($this);

        return true;
    }

    /**
     * Returns whether or not the automatic numbering is activated for this learning path
     *
     * @return bool
     */
    public function usesAutomaticNumbering()
    {
        if (!in_array($this->getAutomaticNumbering(), $this->getAutomaticNumberingOptions()))
        {
            return false;
        }

        return $this->getAutomaticNumbering() != self::AUTOMATIC_NUMBERING_NONE;
    }
}
