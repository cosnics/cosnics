<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessPackageInterface;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ProfileComponent extends Manager implements BreadcrumbLessPackageInterface
{

    /**
     * @return \Chamilo\Libraries\Format\Tabs\Link\LinkTab[]
     */
    public function getAvailableTabs(): array
    {
        $translator = $this->getTranslator();
        $tabs = [];

        $tabs[] = new LinkTab(
            self::ACTION_VIEW_ACCOUNT,
            htmlentities($translator->trans(self::ACTION_VIEW_ACCOUNT . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'),
            $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_ACCOUNT]),
            self::ACTION_VIEW_ACCOUNT == $this->get_action()
        );

        if ($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'allow_change_user_picture']))
        {
            $tabs[] = new LinkTab(
                self::ACTION_CHANGE_PICTURE,
                htmlentities($translator->trans(self::ACTION_CHANGE_PICTURE . 'Title', [], Manager::CONTEXT)),
                new FontAwesomeGlyph('image', ['fa-lg'], null, 'fas'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_CHANGE_PICTURE]),
                self::ACTION_CHANGE_PICTURE == $this->get_action()
            );
        }

        $tabs[] = new LinkTab(
            self::ACTION_USER_SETTINGS,
            htmlentities($translator->trans(self::ACTION_USER_SETTINGS . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('cog', ['fa-lg'], null, 'fas'),
            $this->get_url([self::PARAM_ACTION => self::ACTION_USER_SETTINGS]),
            self::ACTION_USER_SETTINGS == $this->get_action()
        );

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION), new StaticConditionVariable(
                Manager::CONTEXT
            )
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_NAME),
            new StaticConditionVariable('account_fields')
        );
        $condition = new AndCondition($conditions);

        $extra_form = DataManager::retrieve(
            Instance::class, new DataClassRetrieveParameters($condition)
        );

        if ($extra_form instanceof Instance && count($extra_form->get_elements()) > 0)
        {
            $tabs[] = new LinkTab(
                self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION, htmlentities(
                $translator->trans(self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION . 'Title', [], Manager::CONTEXT)
            ), new FontAwesomeGlyph('lightbulb', ['fa-lg'], null, 'fas'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION]),
                self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION == $this->get_action()
            );
        }

        return $tabs;
    }

    abstract public function getContent(): string;

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function renderHeader(string $pageTitle = ''): string
    {
        $availableTabs = $this->getAvailableTabs();

        $html = [];

        $html[] = parent::renderHeader($pageTitle);

        if (count($availableTabs) > 1)
        {
            $tabs = new TabsCollection();

            foreach ($availableTabs as $availableTab)
            {
                $tabs->add($availableTab);
            }

            $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->getContent());
        }
        else
        {
            $html[] = $this->getContent();
        }

        return implode(PHP_EOL, $html);
    }

    public function renderPage(): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
