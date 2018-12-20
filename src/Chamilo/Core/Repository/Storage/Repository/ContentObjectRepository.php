<?php
namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectRepository
{
    const PROPERTY_USED_STORAGE_SPACE = 'used_storage_space';

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param string $contentObjectType
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getUsedStorageSpaceConditionForContentObjectTypeAndUser(
        string $contentObjectType, User $user = null
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable($contentObjectType)
        );

        if ($user instanceof User)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                new StaticConditionVariable($user->getId())
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @param string $contentObjectType
     *
     * @return integer
     * @throws \Exception
     */
    public function getUsedStorageSpaceForContentObjectType(string $contentObjectType)
    {
        return $this->getUsedStorageSpaceForContentObjectTypeAndCondition(
            $contentObjectType, $this->getUsedStorageSpaceConditionForContentObjectTypeAndUser($contentObjectType)
        );
    }

    /**
     * @param string $contentObjectType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \Exception
     */
    public function getUsedStorageSpaceForContentObjectTypeAndCondition(string $contentObjectType, Condition $condition)
    {
        $storageSpaceProperty = $contentObjectType::getStorageSpaceProperty();

        if (empty($storageSpaceProperty))
        {
            return 0;
        }

        $dataClassProperties = new DataClassProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::SUM,
                    new PropertyConditionVariable($contentObjectType, $storageSpaceProperty),
                    self::PROPERTY_USED_STORAGE_SPACE
                )
            ]
        );

        if ($contentObjectType::is_extended())
        {
            $joins = (new Joins(
                [
                    new Join(
                        ContentObject::class, new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                            new PropertyConditionVariable(
                                $contentObjectType, $contentObjectType::PROPERTY_ID
                            )
                        )
                    )
                ]
            ));
        }
        else
        {
            $joins = null;
        }

        $usedStorageSpaceRecord = $this->getDataClassRepository()->record(
            $contentObjectType, new RecordRetrieveParameters($dataClassProperties, $condition, array(), $joins)
        );

        return $usedStorageSpaceRecord[ContentObjectRepository::PROPERTY_USED_STORAGE_SPACE];
    }

    /**
     * @param string $contentObjectType
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     */
    public function getUsedStorageSpaceForContentObjectTypeAndUser(string $contentObjectType, User $user)
    {
        return $this->getUsedStorageSpaceForContentObjectTypeAndCondition(
            $contentObjectType,
            $this->getUsedStorageSpaceConditionForContentObjectTypeAndUser($contentObjectType, $user)
        );
    }
}