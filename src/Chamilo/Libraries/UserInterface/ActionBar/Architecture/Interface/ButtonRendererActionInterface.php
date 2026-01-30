<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonRendererActionInterface
{
    public function renderAction(ButtonActionInterface $button): string;
}