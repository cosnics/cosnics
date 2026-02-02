<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonRendererActionInterface
{
    public function renderAction(ButtonActionInterface $button): string;
}