<?php
namespace Chamilo\Core\Repository\Feedback\Generator;

use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Generator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionsGenerator extends NavigationGenerator
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function run()
    {
        $actions = array();
        
        $application = $this->getApplication();
        $baseParameters = $this->getBaseParameters();
        
        if ($application instanceof FeedbackNotificationSupport)
        {
            if ($this->isAllowedToViewFeedback())
            {
                if ($this->hasNotification())
                {
                    $baseParameters[Manager::PARAM_ACTION] = Manager::ACTION_UNSUBSCRIBER;
                    
                    $actions[] = new Button(
                        Translation::get('StopReceivingNotifications'), 
                        new BootstrapGlyph('remove'), 
                        $application->get_url($baseParameters), 
                        Button::DISPLAY_ICON_AND_LABEL, 
                        false, 
                        'btn-receive-feedback');
                }
                else
                {
                    $baseParameters = $this->getBaseParameters();
                    $baseParameters[Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBER;
                    
                    $actions[] = new Button(
                        Translation::get('ReceiveNotifications'), 
                        new FontAwesomeGlyph('envelope'), 
                        $application->get_url($baseParameters), 
                        Button::DISPLAY_ICON_AND_LABEL, 
                        false, 
                        'btn-receive-feedback');
                }
            }
        }
        
        return $actions;
    }
}