<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBar
{

    /**
     *
     * @var string
     */
    private $searchUrl;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup[]
     */
    private $buttonGroups;

    /**
     *
     * @param string $searchUrl
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup[] $buttonGroups
     */
    public function __construct($searchUrl = null, $buttonGroups = array())
    {
        $this->searchUrl = $searchUrl;
        $this->buttonGroups = $buttonGroups;
    }

    /**
     *
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->searchUrl;
    }

    /**
     *
     * @param string $searchUrl
     */
    public function setSearchUrl($searchUrl)
    {
        $this->searchUrl = $searchUrl;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup[]
     */
    public function getButtonGroups()
    {
        return $this->buttonGroups;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup[] $buttonGroups
     */
    public function setButtonGroups($buttonGroups)
    {
        $this->buttonGroups = $buttonGroups;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $button
     */
    public function addButtonGroup(ButtonGroup $buttonGroup)
    {
        $this->buttonGroups[] = $buttonGroup;
    }
}