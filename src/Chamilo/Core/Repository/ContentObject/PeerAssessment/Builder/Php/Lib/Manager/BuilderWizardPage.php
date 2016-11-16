<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager;

/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */

/**
 * Description of builder_wizard_page
 * 
 * @author jevdheyd
 */
class BuilderWizardPage
{

    private $id;

    private $title;

    private $params = array();

    private $repeats = false;

    private $show_menu = true;

    private $completed = false;

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_title($title)
    {
        $this->title = $title;
    }

    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param array $params
     */
    public function set_params(array $params)
    {
        $this->params = $params;
    }

    public function get_params()
    {
        return $this->params;
    }

    /**
     *
     * @param bool $repeats
     */
    public function set_repeats($repeats)
    {
        $this->repeats = $repeats;
    }

    /**
     * does the page apear more than once?
     * 
     * @return bool
     */
    public function get_repeats()
    {
        return $this->repeats;
    }

    public function set_show_menu($show_menu)
    {
        $this->show_menu = $show_menu;
    }

    public function get_show_menu()
    {
        return $this->show_menu;
    }

    /**
     * is the step completed?
     * 
     * @param bool $completed
     */
    public function set_completed($completed)
    {
        $this->completed = $completed;
    }

    public function get_completed()
    {
        return $this->completed;
    }
}

?>
