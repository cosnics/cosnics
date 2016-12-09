<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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
     *
     * @return string
     */
    public function renderPage()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $actions = $this->getAvailableActions();
        
        $html = array();
        
        $html[] = parent::render_header();
        
        if (count($actions) > 1)
        {
            $tabs = new DynamicVisualTabsRenderer('account', $this->getContent());
            foreach ($actions as $action)
            {
                $selected = ($action == $this->get_action() ? true : false);
                
                $label = htmlentities(Translation::get($action . 'Title'));
                $link = $this->get_url(array(self::PARAM_ACTION => $action));
                
                $tabs->add_tab(
                    new DynamicVisualTab(
                        $action, 
                        $label, 
                        Theme::getInstance()->getImagePath('Chamilo\Core\User', 'Place/' . $action), 
                        $link, 
                        $selected));
            }
            $html[] = $tabs->render();
        }
        else
        {
            $html[] = $this->getContent();
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function getAvailableActions()
    {
        $actions = array();
        
        $actions[] = self::ACTION_VIEW_ACCOUNT;
        
        if (Configuration::get(\Chamilo\Core\User\Manager::context(), 'allow_change_user_picture'))
        {
            $actions[] = self::ACTION_CHANGE_PICTURE;
        }
        
        $actions[] = self::ACTION_USER_SETTINGS;
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_APPLICATION), 
            new StaticConditionVariable(self::context()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_NAME), 
            new StaticConditionVariable('account_fields'));
        $condition = new AndCondition($conditions);
        
        $extra_form = \Chamilo\Configuration\Form\Storage\DataManager::retrieve(
            Instance::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        if ($extra_form instanceof \Chamilo\Configuration\Form\Storage\DataClass\Instance &&
             count($extra_form->get_elements()) > 0)
        {
            $actions[] = self::ACTION_ADDITIONAL_ACCOUNT_INFORMATION;
        }
        
        return $actions;
    }
}
