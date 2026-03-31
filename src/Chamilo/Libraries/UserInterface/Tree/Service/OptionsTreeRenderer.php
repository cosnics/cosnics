<?php
namespace Chamilo\Libraries\UserInterface\Tree\Service;

use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\Tree\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OptionsTreeRenderer
{
    public function __construct(protected OptionsTreeDataProvider $optionsTreeDataProvider)
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice>
     */
    public function getOptions(
        ?string $identifier = null, array $excludedIdentifiers = [], array $disabledIdentifiers = []
    ): ArrayCollection
    {
        $treeNodes = $this->optionsTreeDataProvider->getData($identifier, $excludedIdentifiers);

        $optionTreeChoices = new ArrayCollection();
        $this->processTreeNodes($optionTreeChoices, $treeNodes, 0, $disabledIdentifiers);

        return $optionTreeChoices;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice> $optionTreeChoices
     * @param \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[] $treeNodes
     */
    public function processTreeNodes(
        ArrayCollection $optionTreeChoices, array $treeNodes, int $level = 0, array $disabledIdentifiers = []
    ): void
    {
        foreach ($treeNodes as $treeNode) {
            if ($level > 0) {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $level - 1) . '&mdash; ';
            }
            else {
                $prefix = '';
            }

            $attributes = [];

            if (in_array($treeNode->getIdentifier(), $disabledIdentifiers)) {
                $attributes['disabled'] = 'disabled';
            }

            $optionTreeChoices->add(
                new OptionsTreeChoice($treeNode->getIdentifier(), $prefix . $treeNode->getText(), $attributes)
            );

            $this->processTreeNodes($optionTreeChoices, $treeNode->getChildNodes(), $level + 1, $disabledIdentifiers);
        }
    }
}