<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait ActionButtonRendererTrait
{

    /**
     *
     * @return string
     */
    abstract function renderClasses();

    /**
     *
     * @return string
     */
    abstract function renderTitle();

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton
     */
    abstract function getButton();

    /**
     *
     * @return string
     */
    public function renderLinkOpeningTag()
    {
        $html = array();

        $html[] = '<a';
        $html[] = $this->renderClasses();
        $html[] = $this->renderTitle();
        $html[] = $this->renderAction();
        $html[] = '>';

        return implode(' ', $html);
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = parent::determineClasses();

        if (! $this->getButton()->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }

    /**
     *
     * @return string
     */
    public function renderAction()
    {
        $button = $this->getButton();
        $html = array();

        if ($button->getAction())
        {
            $html[] = 'href="' . htmlentities($button->getAction()) . '"';

            if ($button->getTarget())
            {
                $html[] = 'target="' . $button->getTarget() . '"';
            }

            if ($button->needsConfirmation())
            {
                $html[] = 'onclick="return confirm(\'' . addslashes(htmlentities($button->getConfirmation())) . '\');"';
            }
        }

        return implode(' ', $html);
    }
}
