<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Workspace extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_CREATOR_ID = 'creator_id';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_NAME = 'name';
    public const WORKSPACE_TYPE = 2;

    public function getCreationDate(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    public function getCreatorId(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_ID);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_NAME,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_CREATOR_ID,
                self::PROPERTY_CREATION_DATE
            ]
        );
    }

    protected function getDependencies(array $dependencies = []): array
    {
        return [
            WorkspaceEntityRelation::class => new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
                ), new StaticConditionVariable($this->getId())
            ),
            WorkspaceContentObjectRelation::class => new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                ), new StaticConditionVariable($this->getId())
            ),
            WorkspaceCategoryRelation::class => new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceCategoryRelation::class, WorkspaceCategoryRelation::PROPERTY_WORKSPACE_ID
                ), new StaticConditionVariable($this->getId())
            ),
            WorkspaceUserFavourite::class => new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_WORKSPACE_ID
                ), new StaticConditionVariable($this->getId())
            )
        ];
    }

    public function getDescription(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    public function getHash(): string
    {
        return md5(serialize([__CLASS__, $this->getWorkspaceType(), $this->getId()]));
    }

    public function getName(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public static function getStorageUnitName(): string
    {
        return 'repository_workspace';
    }

    public function getTitle(): string
    {
        return $this->getName();
    }

    public function getWorkspaceType(): int
    {
        return self::WORKSPACE_TYPE;
    }

    public function setCreationDate(int $creationDate)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creationDate);
    }

    public function setCreatorId(int $creatorId)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATOR_ID, $creatorId);
    }

    public function setDescription(?string $description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    public function setName(string $name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }
}