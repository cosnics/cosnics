<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\SubButtonInterface;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonHeader extends AbstractButton implements SubButtonInterface
{
    public function __construct(string $label, array $classes = [])
    {
        parent::__construct($label, null, self::DISPLAY_LABEL, $classes);
    }
}