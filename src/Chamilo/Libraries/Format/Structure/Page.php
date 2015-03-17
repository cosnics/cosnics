<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Page
{
    const VIEW_MODE_FULL = 1;
    const VIEW_MODE_HEADERLESS = 2;

    /**
     *
     * @var integer
     */
    private $viewMode;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Header
     */
    private $header;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Footer
     */
    private $footer;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var string
     */
    private $section;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Page
     */
    protected static $instance = null;

    /**
     * Return 'this' as singleton
     *
     * @return \Chamilo\Libraries\Format\Structure\Page
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            $header = new Header(self :: VIEW_MODE_FULL, Translation :: get_instance()->get_language(), 'ltr');
            $footer = new Footer(self :: VIEW_MODE_FULL);

            self :: $instance = new static(self :: VIEW_MODE_FULL, $header, $footer);
        }

        return static :: $instance;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $viewMode
     */
    public function __construct($viewMode = self :: VIEW_MODE_FULL, Header $header, Footer $footer)
    {
        $this->viewMode = $viewMode;
        $this->header = $header;
        $this->footer = $footer;
    }

    /**
     *
     * @return integer
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     *
     * @param integer $viewMode
     */
    public function setViewMode($viewMode)
    {
        $this->viewMode = $viewMode;

        $this->getHeader()->setViewMode($this->viewMode);
        $this->getFooter()->setViewMode($this->viewMode);
    }

    /**
     *
     * @return boolean
     */
    public function isHeaderless()
    {
        return $this->getViewMode() == self :: VIEW_MODE_HEADERLESS;
    }

    /**
     *
     * @return boolean
     */
    public function isFullPage()
    {
        return $this->getViewMode() == self :: VIEW_MODE_FULL;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;

        $this->getHeader()->setApplication($this->application);
        $this->getFooter()->setApplication($this->application);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Header $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Footer
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Footer $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    /**
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     *
     * @param string $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }
}