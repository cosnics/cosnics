<?php
namespace Chamilo\Libraries\Format\Tabs\Link;

use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTabRenderer
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function renderNavigation(LinkTab $tab): string
    {
        $classes = [];

        if ($tab->isSelected())
        {
            $classes[] = 'active';
        }

        $classes[] = 'pull-' . $tab->getPosition();

        $html = [];
        $html[] = '<li class="' . implode(' ', $classes) . '">';

        $link = [];
        $link[] = '<a';

        if ($tab->getLink() && $tab->opensInWindow())
        {
            $link[] = 'href="' . $tab->getLink() . '"';

            if ($tab->hasConfirmationMessage())
            {
                $link[] = 'onclick="return confirm(\'' . addslashes(
                        htmlentities(
                            $tab->getConfirmationMessage() === true ? $this->getTranslator()->trans(
                                'Confirm', [], StringUtilities::LIBRARIES
                            ) : $tab->getConfirmationMessage()
                        )
                    ) . '\');"';
            }
        }
        elseif ($tab->getLink() && $tab->opensInPopup())
        {
            $link[] = 'href="" onclick="javascript:openPopup(\'' . $tab->getLink() . '\'); return false"';
        }
        else
        {
            $link[] = 'style="cursor: default;"';
        }

        $link[] = '>';

        $html[] = implode(' ', $link);

        if ($tab->getInlineGlyph() && $tab->isIconVisible())
        {
            $html[] = $tab->getInlineGlyph()->render();
        }

        if ($tab->getLabel() && $tab->isTextVisible())
        {
            $html[] = '<span class="title">' . $tab->getLabel() . '</span>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}
