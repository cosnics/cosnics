<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Form\ManageForm;
use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Interface to manage rights
 */
class ManagerComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $form = new ManageForm(
            $this->get_url(), 
            $this->get_context(), 
            $this->get_locations(), 
            $this->get_available_rights(), 
            $this->get_entities());
        
        if ($form->validate())
        {
            $succes = $form->handle_form_submit();
            
            $message = Translation::get($succes ? 'RightsChanged' : 'RightsNotChanged');
            $this->redirect($message, ! $succes, array(self::PARAM_ACTION => self::ACTION_EDIT_ADVANCED_RIGHTS));
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<br />';
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the actionbar;
     * 
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            // Add the simple rights editor button
            $commonActions->addButton(
                new Button(
                    Translation::get('AdvancedRightsEditor'), 
                    Theme::getInstance()->getCommonImagePath('Action/Config'), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EDIT_ADVANCED_RIGHTS)), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
