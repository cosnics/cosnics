<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Traits\TranslatableItemTrait;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationItemRenderer extends ItemRenderer implements SelectableItemInterface, TranslatableItemInterface
{
    use TranslatableItemTrait;

    public const CONFIGURATION_APPLICATION = 'application';
    public const CONFIGURATION_COMPONENT = 'component';
    public const CONFIGURATION_EXTRA_PARAMETERS = 'extra_parameters';
    public const CONFIGURATION_USE_TRANSLATION = 'use_translation';

    private RegistrationConsulter $registrationConsulter;

    private UrlGenerator $urlGenerator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, RegistrationConsulter $registrationConsulter,
        UrlGenerator $urlGenerator, array $fallbackIsoCodes
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->registrationConsulter = $registrationConsulter;
        $this->urlGenerator = $urlGenerator;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function render(Item $item, User $user): string
    {
        if (!$this->isItemVisibleForUser($item, $user))
        {
            return '';
        }

        $html = [];

        $html[] = '<li class="' . ($this->isSelected($item, $user) ? 'active' : '') . '">';

        $title = $this->renderTitle($item);

        $html[] = '<a href="' . $this->getApplicationItemUrl($item) . '">';

        if ($item->showIcon())
        {
            if (!empty($item->getIconClass()))
            {
                $glyph = new FontAwesomeGlyph($item->getIconClass(), ['fa-2x'], $title, 'fas');
            }
            else
            {
                $glyph = new NamespaceIdentGlyph(
                    $item->getSetting(self::CONFIGURATION_APPLICATION), false, false, false, IdentGlyph::SIZE_MEDIUM,
                    [], $title
                );
            }

            $html[] = $glyph->render();
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    protected function getApplicationItemUrl(Item $item): string
    {
        $application = $item->getSetting(self::CONFIGURATION_APPLICATION);

        if ($application == 'root')
        {
            return $this->getUrlGenerator()->fromParameters();
        }

        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = $application;

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component)
        {
            $parameters[Application::PARAM_ACTION] = $component;
        }

        $extraParameters = $item->getSetting(self::CONFIGURATION_EXTRA_PARAMETERS);

        if ($extraParameters)
        {
            parse_str($extraParameters, $parsedExtraParameters);

            foreach ($parsedExtraParameters as $key => $value)
            {
                $parameters[$key] = $value;
            }
        }

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isItemVisibleForUser(Item $item, User $user): bool
    {
        $application = $item->getSetting(self::CONFIGURATION_APPLICATION);

        $isAuthorized = $this->getAuthorizationChecker()->isAuthorized($user, $application);
        $isActiveApplication = $this->getRegistrationConsulter()->isContextRegisteredAndActive($application);

        return $isAuthorized && $isActiveApplication;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentAction = $request->query->get(Application::PARAM_ACTION);

        if ($currentContext != $item->getSetting(self::CONFIGURATION_APPLICATION))
        {
            return false;
        }

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component && $currentAction != $component)
        {
            return false;
        }

        return true;
    }

    public function renderTitle(Item $item): string
    {
        if ($item->getSetting(self::CONFIGURATION_USE_TRANSLATION))
        {
            return $this->getTranslator()->trans('TypeName', [], $item->getSetting(self::CONFIGURATION_APPLICATION));
        }

        return $this->determineItemTitleForCurrentLanguage($item);
    }
}