<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Knp\Menu\Matcher\Voter\VoterInterface;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeMenuVoter implements VoterInterface
{
    protected string $selectedItemIdentifier;

    public function __construct(string $selectedItemIdentifier)
    {
        $this->selectedItemIdentifier = $selectedItemIdentifier;
    }

    public function matchItem($item): ?bool
    {
        if ($item->getAttribute('id') === $this->selectedItemIdentifier)
        {
            return true;
        }

        return null;
    }
}