<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @package repository.lib.content_object.rubric
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * A Rubric
 */
class Rubric extends ContentObject implements Versionable
{
    const PROPERTY_ACTIVE_RUBRIC_DATA_ID = 'active_rubric_data_id';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        $propertyNames = parent::get_additional_property_names();
        $propertyNames[] = self::PROPERTY_ACTIVE_RUBRIC_DATA_ID;

        return $propertyNames;
    }

    /**
     * @return int
     */
    public function getActiveRubricDataId()
    {
        return $this->get_additional_property(self::PROPERTY_ACTIVE_RUBRIC_DATA_ID);
    }

    /**
     * @param int $activeRubricDataId
     *
     * @return $this
     */
    public function setActiveRubricDataId(int $activeRubricDataId)
    {
        $this->set_additional_property(self::PROPERTY_ACTIVE_RUBRIC_DATA_ID, $activeRubricDataId);

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if (!isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }

        return $this->container;
    }

    /**
     * @return RubricService|object
     */
    protected function getRubricService()
    {
        return $this->getContainer()->get(RubricService::class);
    }

    /**
     * @param bool $create_in_batch
     *
     * @return bool
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function create($create_in_batch = false)
    {
        if (!parent::create($create_in_batch))
        {
            return false;
        }

        return true;
    }
}
