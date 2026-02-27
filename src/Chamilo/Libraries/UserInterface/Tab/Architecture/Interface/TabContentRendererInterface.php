<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TabContentRendererInterface
{
    public function renderContent(TabContentInterface $tab, ?string $selectedTab = null): string;
}