<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Manager;

/**
 * Simple interface to edit rights
 * 
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 */
abstract class RightsEditorComponent extends Manager
{

    public function render_header($pageTitle = null)
    {
        $html = array();
        
        $html[] = parent::render_header();
        
        $additional_information = $this->get_additional_information();
        
        if ($additional_information)
        {
            $html[] = '<div style="background-color: #E5EDF9; border: 1px solid #B9D0EF; color: #272761; margin-top: 5px;
                margin-bottom: 5px; padding: 7px;">';
            $html[] = $additional_information;
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }
}
