<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface FooterRendererInterface
{
    public function render(): string;
}