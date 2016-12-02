<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager;

use Chamilo\Libraries\Platform\Translation;

/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */

/**
 * Description of builder_wizard
 * 
 * @author jevdheyd
 */
class BuilderWizard
{

    private $builder;

    private $pages;

    private $complete = false;

    private $menu;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     *
     * @param PeerAssessmentBuilderWizardPage $page
     */
    public function add_page(BuilderWizardPage $page)
    {
        $this->pages[] = $page;
    }

    /**
     * creates a html menu if the wizard is not considered to be completed
     * 
     * @param string $page_id
     * @return string
     */
    public function display($page_id = null, $default)
    {
        if (is_null($this->menu))
        {
            if (! $this->complete)
            {
                $previous = null;
                $current = null;
                $next = null;
                $found = false;
                $display = true;
                
                foreach ($this->pages as $page)
                {
                    /*
                     * indicate that current step is found - if page_id matches the current page_id - if the current
                     * page_id is null and page_id matches the default page_id
                     */
                    if (($page->get_id() == $page_id || is_null($page_id) && $page->get_id() == $default) && ! $found)
                    {
                        $found = true;
                        $current = $page;
                        if (! $page->get_show_menu())
                        {
                            $display = false;
                            break;
                        }
                        continue;
                    }
                    
                    if (! $found && ! $page->get_completed())
                    {
                        $previous = $page;
                    }
                    elseif (! $page->get_completed())
                    {
                        $next = $page;
                        break;
                    }
                }
                
                // build menu
                
                if (! is_null($previous))
                    $previous = '<a href="' . $this->builder->get_url($previous->get_params()) . '"> << ' .
                         Translation::get($previous->get_title()) . '</a>';
                if (! is_null($next))
                    $next = '<a href="' . $this->builder->get_url($next->get_params()) . '"> ' .
                         Translation::get($next->get_title()) . ' >></a>';
                
                if (($previous || $next) && $found && $display)
                    $this->menu = '<div class="wizard"><h3>' . Translation::get('WizardTitle') . '</h3>' . $previous .
                         $current . ' - ' . $next . '</div>';
            }
        }
        
        return $this->menu;
    }

    /**
     * status of the wizard either false or a page_id
     * 
     * @param type $status
     */
    function set_complete($complete)
    {
        $this->complete = $complete;
    }
}

?>
