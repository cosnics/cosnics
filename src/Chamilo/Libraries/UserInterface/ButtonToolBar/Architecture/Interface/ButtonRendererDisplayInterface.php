<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonRendererDisplayInterface
{
    public function renderInlineGlyphAndLabel(ButtonDisplayInterface $button): string;
}