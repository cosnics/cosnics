<?php

namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\Viewer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ApplicationConfiguration extends \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration
{
    const MAXIMUM_SELECT_MULTIPLE = Manager::SELECT_MULTIPLE;
    const MAXIMUM_SELECT_SINGLE = Manager::SELECT_SINGLE;

    /**
     * @var string[]
     */
    protected $allowedContentObjectTypes = [];

    /**
     * @var ContentObject[]
     */
    protected $userTemplates = [];

    /**
     * @var bool
     */
    protected $tabsDisabled = false;

    /**
     * @var bool
     */
    protected $breadcrumbsDisabled = false;

    /**
     * @var int
     */
    protected $maximumSelect = self::MAXIMUM_SELECT_MULTIPLE;

    /**
     * @var int[]
     */
    protected $excludedContentObjectIds = [];

    /**
     * @return ContentObject[]
     */
    public function getUserTemplates(): ?array
    {
        return $this->userTemplates;
    }

    /**
     * @param ContentObject[] $templates
     *
     * @return ApplicationConfiguration
     */
    public function setUserTemplates(array $templates): ApplicationConfiguration
    {
        $this->userTemplates = $templates;

        return $this;
    }

    /**
     * @return bool
     */
    public function areTabsDisabled(): bool
    {
        return $this->tabsDisabled;
    }

    /**
     * @return $this
     */
    public function disableTabs(): ApplicationConfiguration
    {
        $this->tabsDisabled = true;

        return $this;
    }

    public function areBreadcrumbsDisabled(): bool
    {
        return $this->breadcrumbsDisabled;
    }

    /**
     * @return $this
     */
    public function disableBreadcrumbs(): ApplicationConfiguration
    {
        $this->breadcrumbsDisabled = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaximumSelect(): ?int
    {
        return $this->maximumSelect;
    }

    /**
     * @param int $maximumSelect
     *
     * @return ApplicationConfiguration
     */
    public function setMaximumSelect(int $maximumSelect): ApplicationConfiguration
    {
        if ($maximumSelect < 0)
        {
            throw new \InvalidArgumentException('The given maximum select can not be smaller than 0');
        }

        $this->maximumSelect = $maximumSelect;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllowedContentObjectTypes(): ?array
    {
        return $this->allowedContentObjectTypes;
    }

    /**
     * @param string[] $allowedContentObjectTypes
     *
     * @return ApplicationConfiguration
     */
    public function setAllowedContentObjectTypes(array $allowedContentObjectTypes): ApplicationConfiguration
    {
        foreach ($allowedContentObjectTypes as $allowedContentObjectType)
        {
            if (!class_exists($allowedContentObjectType) ||
                !is_subclass_of($allowedContentObjectType, ContentObject::class))
            {
                throw new \InvalidArgumentException(
                    sprintf('The given type %s is not a valid content object type', $allowedContentObjectType)
                );
            }
        }

        $this->allowedContentObjectTypes = $allowedContentObjectTypes;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getExcludedContentObjectIds(): ?array
    {
        return $this->excludedContentObjectIds;
    }

    /**
     * @param int[] $excludedContentObjectIds
     *
     * @return ApplicationConfiguration
     */
    public function setExcludedContentObjectIds(array $excludedContentObjectIds): ApplicationConfiguration
    {
        $this->excludedContentObjectIds = $excludedContentObjectIds;

        return $this;
    }
}
