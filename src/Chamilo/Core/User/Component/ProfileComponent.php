<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ProfileComponent extends Manager implements NoContextComponent
{

    /**
     * @return \Chamilo\Libraries\Format\Tabs\Link\LinkTab[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getAvailableTabs()
    {
        $tabs = [];

        $tabs[] = new LinkTab(
            self::ACTION_VIEW_ACCOUNT, htmlentities(Translation::get(self::ACTION_VIEW_ACCOUNT . 'Title')),
            new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'),
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_ACCOUNT)),
            self::ACTION_VIEW_ACCOUNT == $this->get_action()
        );

        if (Configuration::get(Manager::context(), 'allow_change_user_picture'))
        {
            $tabs[] = new LinkTab(
                self::ACTION_CHANGE_PICTURE, htmlentities(Translation::get(self::ACTION_CHANGE_PICTURE . 'Title')),
                new FontAwesomeGlyph('image', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CHANGE_PICTURE)),
                self::ACTION_CHANGE_PICTURE == $this->get_action()
            );
        }

        $tabs[] = new LinkTab(
            self::ACTION_USER_SETTINGS, htmlentities(Translation::get(self::ACTION_USER_SETTINGS . 'Title')),
            new FontAwesomeGlyph('cog', array('fa-lg'), null, 'fas'),
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER_SETTINGS)),
            self::ACTION_USER_SETTINGS == $this->get_action()
        );

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION),
            new StaticConditionVariable(self::context())
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
                self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION,
                htmlentities(Translation::get(self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION . 'Title')),
                new FontAwesomeGlyph('lightbulb', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION)),
                self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION == $this->get_action()
            );
        }

        return $tabs;
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function renderPage()
    {
        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function render_header(string $pageTitle = ''): string
    {
        $availableTabs = $this->getAvailableTabs();

        $html = [];

        $html[] = parent::render_header($pageTitle);

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
}
