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
     * @return string[]
     */
    public function determineClasses(): array
    {
        $classes = parent::determineClasses();

        if (!$this->getButton()->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton
     */
    abstract public function getButton();

    public function renderAction(): string
    {
        $button = $this->getButton();
        $html = [];

        if ($button->getAction())
        {
            $html[] = 'href="' . htmlentities($button->getAction()) . '"';

            if ($button->getTarget())
            {
                $html[] = 'target="' . $button->getTarget() . '"';
            }

            if ($button->needsConfirmation())
            {
                $html[] = 'onclick="return confirm(\'' . addslashes(htmlentities($button->getConfirmationMessage())) .
                    '\');"';
            }
        }

        return implode(' ', $html);
    }

    abstract public function renderClasses(): string;

    public function renderLinkOpeningTag(): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = $this->renderClasses();
        $html[] = $this->renderTitle();
        $html[] = $this->renderAction();
        $html[] = '>';

        return implode(' ', $html);
    }

    abstract public function renderTitle(): string;
}
