<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonRendererDisplayInterface
{
    public function renderInlineGlyphAndLabel(ButtonDisplayInterface $button): string;
}