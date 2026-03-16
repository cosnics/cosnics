<?php
namespace Chamilo\Libraries\UserInterface\Tree\Service;

/**
 * @package Chamilo\Libraries\UserInterface\Tree\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OptionsTreeRenderer
{
    protected OptionsTreeDataProvider $optionsTreeDataProvider;

    public function __construct(OptionsTreeDataProvider $optionsTreeDataProvider)
    {
        $this->optionsTreeDataProvider = $optionsTreeDataProvider;
    }

    public function getOptions(?string $identifier = null): array
    {
        $treeNodes = $this->getOptionsTreeDataProvider()->getData($identifier);
        $options = [];
        $this->processTreeNodes($options, $treeNodes);

        return $options;
    }

    public function getOptionsTreeDataProvider(): OptionsTreeDataProvider
    {
        return $this->optionsTreeDataProvider;
    }

    /**
     * @param string[] $options
     * @param \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[] $treeNodes
     */
    public function processTreeNodes(array &$options, array $treeNodes, int $level = 0): void
    {
        foreach ($treeNodes as $treeNode) {
            if ($level > 0) {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $level - 1) . '&mdash; ';
            }
            else {
                $prefix = '';
            }

            $options[$prefix . $treeNode->getText()] = $treeNode->getIdentifier();

            $this->processTreeNodes($options, $treeNode->getChildNodes(), $level + 1);
        }
    }
}