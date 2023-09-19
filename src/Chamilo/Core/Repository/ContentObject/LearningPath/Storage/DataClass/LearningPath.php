<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Architecture\DisplayAndBuildSupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass
 */
class LearningPath extends ContentObject implements ComplexContentObjectSupportInterface, DisplayAndBuildSupport
{
    public const AUTOMATIC_NUMBERING_DIGITS = 'digits';

    public const AUTOMATIC_NUMBERING_NONE = 'none';

    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\LearningPath';

    public const PROPERTY_AUTOMATIC_NUMBERING = 'automatic_numbering';
    public const PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'enforce_default_traversing_order';

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
    public function create(): bool
    {
        if (!parent::create())
        {
            return false;
        }

        $user = new User();
        $user->setId($this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID));

        $this->getTreeNodeDataService()->createTreeNodeDataForLearningPath($this, $user);

        return true;
    }

    /**
     * Delete a LearningPath and all of it's node data
     *
     * @param bool $only_version
     *
     * @return bool Returns whether the delete was succesfull.
     */
    public function delete($only_version = false): bool
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
        return (bool) $this->getAdditionalProperty(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_AUTOMATIC_NUMBERING, self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER];
    }

    /**
     * Returns the automatic numbering
     *
     * @return string
     */
    public function getAutomaticNumbering()
    {
        return $this->getAdditionalProperty(self::PROPERTY_AUTOMATIC_NUMBERING);
    }

    /**
     * Returns a list of automatic numbering options
     *
     * @return string[]
     */
    public static function getAutomaticNumberingOptions()
    {
        return [self::AUTOMATIC_NUMBERING_NONE, self::AUTOMATIC_NUMBERING_DIGITS];
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     * @throws \Exception
     */
    protected function getLearningPathService()
    {
        return $this->getService(LearningPathService::class);
    }

    public function getSession(): SessionInterface
    {
        return $this->getService(SessionInterface::class);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_learning_path';
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     * @throws \Exception
     */
    protected function getTreeNodeDataService()
    {
        return $this->getService(TreeNodeDataService::class);
    }

    /**
     * @return array
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function get_allowed_types(): array
    {
        $classNameUtilities = $this->getClassnameUtilities();
        $registrationConsulter = $this->getRegistrationConsulter();

        $registrations = $registrationConsulter->getIntegrationRegistrations(LearningPath::CONTEXT);
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
            $parentRegistration = $registrationConsulter->getRegistrationForContext($parentContext);

            if ($parentContext == 'Chamilo\Core\Repository\ContentObject\Section')
            {
                continue;
            }

            if ($parentRegistration[Registration::PROPERTY_TYPE] == Manager::CONTEXT . '\ContentObject')
            {
                $namespace = $classNameUtilities->getNamespaceParent(
                    $registration[Registration::PROPERTY_CONTEXT], 6
                );
                $types[] =
                    $namespace . '\Storage\DataClass\\' . $classNameUtilities->getPackageNameFromNamespace($namespace);
            }
        }

        return $types;
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

        $this->setAdditionalProperty(self::PROPERTY_AUTOMATIC_NUMBERING, $automaticNumberingOption);
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

        $this->setAdditionalProperty(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER, $enforceDefaultTraversingOrder);
    }

    public function update($trueUpdate = true): bool
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
