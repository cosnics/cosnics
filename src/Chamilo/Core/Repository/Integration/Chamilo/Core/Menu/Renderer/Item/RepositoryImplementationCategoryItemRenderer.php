<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\ItemCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationItem;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationCategoryItemRenderer extends ItemRenderer
{
    /**
     * @var \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemCacheService $itemCacheService,
        Theme $themeUtilities, ChamiloRequest $request, ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $themeUtilities, $request);

        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($item, $user))
        {
            return '';
        }

        $html = array();

        $selected = $this->isSelected($item);

        $html[] = '<li class="dropdown' . ($selected ? ' active' : '') . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $title = $this->getTranslator()->trans('Instance', [], 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu');

        if ($item->showIcon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = $this->getThemeUtilities()->getImagePath(
                $integrationNamespace, 'RepositoryImplementationCategory' . ($selected ? 'Selected' : '')
            );

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '" src="' . $imagePath .
                '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        $html[] = $this->renderInstances($item, $user);
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     * @todo This shouldn't really be here like this
     */
    protected function findInstances()
    {
        return \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
            Instance::class, new DataClassRetrievesParameters()
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationCategoryItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return mixed
     */
    public function isItemVisibleForUser(Item $item, User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\Repository');
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationCategoryItem $item
     *
     * @return boolean
     */
    public function isSelected(Item $item)
    {
        // TODO;
        return false;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function renderInstances(Item $item, User $user)
    {
        $html = array();
        $instances = $this->findInstances();

        if ($instances->size())
        {
            $html[] = '<ul class="dropdown-menu">';

            while ($instance = $instances->next_result())
            {
                if (!$instance->is_enabled())
                {
                    continue;
                }

                $instanceItem = new RepositoryImplementationItem();
                $instanceItem->set_implementation($instance->get_implementation());
                $instanceItem->set_instance_id($instance->get_id());
                $instanceItem->set_name($instance->get_title());
                $instanceItem->setParentId($item->getId());
                $instanceItem->setDisplay(Item::DISPLAY_TEXT);

                $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($instanceItem);

                $html[] = $itemRenderer->render($instanceItem, $user);
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }
}