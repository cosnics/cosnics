<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
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
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[] $treeNodes
     */
    public function processTreeNodes(array &$options, array $treeNodes, int $level = 0): void
    {
        foreach ($treeNodes as $treeNode)
        {
            if ($level > 0)
            {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $level - 1) . '&mdash; ';
            }
            else
            {
                $prefix = '';
            }

            $options[$treeNode->getIdentifier()] = $prefix . $treeNode->getText();

            $this->processTreeNodes($options, $treeNode->getChildNodes(), $level + 1);
        }
    }
}