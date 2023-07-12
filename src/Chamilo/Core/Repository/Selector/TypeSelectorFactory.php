<?php
namespace Chamilo\Core\Repository\Selector;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Selector\Option\ContentObjectTypeSelectorOption;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\Repository\Selector
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class TypeSelectorFactory
{
    use DependencyInjectionContainerTrait;

    public const MODE_CATEGORIES = 1;
    public const MODE_FLAT_LIST = 2;

    /**
     * @var bool
     */
    protected $defaultSorting;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var string[]
     */
    private $contentObjectTypes;

    /**
     * @var int
     */
    private $userIdentifier;

    /**
     * @param string[] $contentObjectTypes
     * @param int $userIdentifier
     * @param int $mode
     * @param bool $defaultSorting
     */
    public function __construct(
        $contentObjectTypes = null, $userIdentifier = null, $mode = self::MODE_CATEGORIES, $defaultSorting = true
    )
    {
        $this->contentObjectTypes = $contentObjectTypes;
        $this->userIdentifier = $userIdentifier;
        $this->mode = $mode;
        $this->defaultSorting = $defaultSorting;
    }

    /**
     * @return \Chamilo\Core\Repository\Selector\TypeSelector
     */
    public function buildTypeSelector()
    {
        $helperTypes = DataManager::get_active_helper_types();
        $typeSelector = new TypeSelector();

        $contexts = [];

        foreach ($this->getContentObjectTypes() as $contentObjectType)
        {
            $classnameUtilities = ClassnameUtilities::getInstance();
            $namespace = $classnameUtilities->getNamespaceFromClassname($contentObjectType);
            $contexts[] = $classnameUtilities->getNamespaceParent($namespace, 2);
        }

        $templateRegistrations =
            $this->getTemplateRegistrationConsulter()->getTemplateRegistrationsByTypesAndUserIdentifier(
                $contexts, $this->getUserIdentifier()
            );

        foreach ($templateRegistrations as $templateRegistration)
        {
            $type = $templateRegistration->get_content_object_type() . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                    $templateRegistration->get_content_object_type()
                );

            if ($this->getRegistrationConsulter()->isContextRegisteredAndActive($templateRegistration->get_content_object_type()))
            {
                if (in_array($type, $helperTypes))
                {
                    continue;
                }

                $registration = $this->getRegistrationConsulter()->getRegistrationForContext(
                    $templateRegistration->get_content_object_type()
                );

                $contentObjectName = $templateRegistration->get_template()->translate('TypeName');

                $option =
                    new ContentObjectTypeSelectorOption($contentObjectName, (int) $templateRegistration->get_id());

                if ($this->mode == self::MODE_CATEGORIES)
                {
                    $categoryType = $registration[Registration::PROPERTY_CATEGORY];

                    if (!$typeSelector->category_type_exists($categoryType))
                    {
                        $typeSelectorCategory = new TypeSelectorCategory(
                            $categoryType, Translation::get(
                            (string) StringUtilities::getInstance()->createString($categoryType)->upperCamelize()
                        )
                        );

                        $typeSelector->add_category($typeSelectorCategory);
                    }

                    $typeSelectorCategory = $typeSelector->get_category_by_type($categoryType);
                    $typeSelectorCategory->add_option($option);
                }
                else
                {
                    $typeSelector->add_option($option);
                }
            }
        }

        if ($this->defaultSorting)
        {
            $typeSelector->sort();
        }

        return $typeSelector;
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     * @throws \Exception
     */
    public function getConfigurablePathBuilder()
    {
        return $this->getService(
            ConfigurablePathBuilder::class
        );
    }

    /**
     * @return string[]
     */
    public function getContentObjectTypes()
    {
        return $this->contentObjectTypes;
    }
    
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->getService(RegistrationConsulter::class);
    }

    public function getTemplateRegistrationConsulter(): TemplateRegistrationConsulter
    {
        return $this->getService(TemplateRegistrationConsulter::class);
    }

    /**
     * @throws \Exception
     */
    public function getTypeSelector(): TypeSelector
    {
        $cacheAdapter = $this->getTypeSelectorCacheAdapter();

        $cacheIdentifier = md5(
            serialize([$this->getContentObjectTypes(), $this->getUserIdentifier(), $this->mode, $this->defaultSorting])
        );

        try
        {
            $cacheItem = $cacheAdapter->getItem($cacheIdentifier);

            if (!$cacheItem->isHit())
            {
                $cacheItem->set($this->buildTypeSelector());
                $cacheAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException $e)
        {
            return new TypeSelector();
        }
    }

    protected function getTypeSelectorCacheAdapter(): FilesystemAdapter
    {
        return $this->getService('Chamilo\Core\Repository\Service\TypeSelectorCacheAdapter');
    }

    /**
     * @return int
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     * @param string[] $contentObjectTypes
     */
    public function setContentObjectTypes($contentObjectTypes)
    {
        $this->contentObjectTypes = $contentObjectTypes;
    }

    /**
     * @param int $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }
}