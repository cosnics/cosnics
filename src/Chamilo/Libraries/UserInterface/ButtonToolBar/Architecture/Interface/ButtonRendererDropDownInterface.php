<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonRendererDropDownInterface
{
    /**
     * @return string[]
     */
    public function determineDropdownClasses(ButtonDropDownCollectionInterface $dropDownButton): array;

    public function renderDropDownButtons(ButtonDropDownCollectionInterface $dropDownButton): string;
}