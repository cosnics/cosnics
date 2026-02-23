<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TabNavigationRendererInterface
{
    public function renderNavigation(TabNavigationInterface $tab): string;
}