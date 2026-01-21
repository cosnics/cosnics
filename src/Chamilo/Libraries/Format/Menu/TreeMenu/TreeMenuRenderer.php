<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeMenuRenderer
{
    protected ResourceManager $resourceManager;

    public function __construct(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param string[] $selectedPathIdentifiers
     */
    public function render(string $name, string $parameterName, string $dataUrl, array $selectedPathIdentifiers): string
    {
        $selectedIdentifier = $selectedPathIdentifiers[array_key_last($selectedPathIdentifiers)];
        $jsonEncodedSelectedPathIdentifiers = json_encode($selectedPathIdentifiers);

        $html = [];

        $html[] = $this->getResourceManager()->getResourceHtml(
            'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/jstree.min.js'
        );
        $html[] = $this->getResourceManager()->getResourceHtml(
            'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/themes/default/style.min.css'
        );

        $html[] = '<div id="' . $name . '"></div>';

        $html[] = <<< EOT
<script type="text/javascript">
$(function () {
    $('#{$name}').jstree({
        core: {
            data: {
                url: '{$dataUrl}',
                data: function (node) {
                    if (node.id != '#') {
                        return {'{$parameterName}': node.id};
                    }
                    else {
                        return;
                    }
                }
            },
            themes: {
                name: false,
                url: false,
                dots: true,
                icons: true,
                ellipsis: true,
                stripes: false,
                responsive: true,
            }
        }
    }).on("select_node.jstree", function (event, data) {
        if (data.event) {
            if (data.event.type === 'click') {
                window.location = data.node.a_attr.href;
            }
        }
        $(this).jstree(true).open_node(data.node);
    }).on("open_node.jstree", function (event, data) {
        var tree = $(this).jstree(true);
        
        if (data.node.id === '{$selectedIdentifier}') {
            tree.select_node(data.node);
        }

        data.node.children.forEach(function (childNode) {
            if (childNode === '{$selectedIdentifier}') {
                tree.select_node(childNode);
            }
        });

    }).on("ready.jstree", function (e, data) {
        var tree = $(this).jstree(true);
        var pathIdentifiers = {$jsonEncodedSelectedPathIdentifiers};

        pathIdentifiers.forEach(function (pathIdentifier) {
            tree.open_node(pathIdentifier);
        });
    });
});
</script>
EOT;

        return implode(PHP_EOL, $html);
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }
}