<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Publication;

/**
 * Custom publication handler for the forum tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationHandler extends \Chamilo\Application\Weblcms\Publication\ContentObjectPublicationHandler
{

    /**
     * Returns the necessary parameters to redirect to the complex display
     * 
     * @return mixed
     */
    protected function getDisplayParameters()
    {
        $parameters = parent::getDisplayParameters();
        
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        
        return $parameters;
    }
}