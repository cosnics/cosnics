<?php
namespace Chamilo\Core\Repository\Service\Menu;

use Chamilo\Core\Menu\Architecture\Interfaces\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceItemRenderer extends ItemRenderer implements SelectableItemInterface, ConfigurableItemInterface
{
    public const CONFIGURATION_NAME = 'name';
    public const CONFIGURATION_WORKSPACE_ID = 'workspace_id';

    protected UrlGenerator $urlGenerator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $selected = $this->isSelected($item, $user);

        $workspaceUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_WORKSPACE_ID => $item->getSetting(self::CONFIGURATION_WORKSPACE_ID)
            ]
        );

        $html = [];

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $workspaceUrl . '">';
        $title = $this->renderTitleForCurrentLanguage($item);

        if ($item->showIcon())
        {
            $glyph = $this->getRendererTypeGlyph();
            $glyph->setExtraClasses(['fa-2x']);
            $glyph->setTitle($title);

            $html[] = $glyph->render();
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function addConfigurationToForm(FormValidator $formValidator): void
    {
        $translator = $this->getTranslator();

        $formValidator->addElement(
            'category', $translator->trans('Properties', [], \Chamilo\Core\Menu\Manager::CONTEXT)
        );

        //TODO: Make this a selector of some kind, should only list workspaces available for everyone?
        $formValidator->add_textfield(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_WORKSPACE_ID . ']',
            $translator->trans('WorkspaceId', [], Manager::CONTEXT), false
        );

        $formValidator->add_textfield(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_NAME . ']',
            $translator->trans('WorkspaceName', [], Manager::CONTEXT), false
        );
    }

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array
    {
        return [self::CONFIGURATION_NAME, self::CONFIGURATION_WORKSPACE_ID];
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('hdd', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('WorkspaceItem', [], \Chamilo\Core\Menu\Manager::CONTEXT);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentWorkspace = $request->query->get(Manager::PARAM_WORKSPACE_ID);

        return $currentContext == Manager::CONTEXT &&
            $currentWorkspace == $item->getSetting(self::CONFIGURATION_WORKSPACE_ID);
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $item->getSetting(self::CONFIGURATION_NAME);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->renderTitleForCurrentLanguage($item);
    }
}