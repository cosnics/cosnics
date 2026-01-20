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

    protected TreeMenuDataProvider $treeMenuDataProvider;

    public function __construct(TreeMenuDataProvider $treeMenuDataProvider, ResourceManager $resourceManager)
    {
        $this->treeMenuDataProvider = $treeMenuDataProvider;
        $this->resourceManager = $resourceManager;
    }

    public function render(string $name, string $dataUrl, string $selectedItemIdentifier): string
    {
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
$(function () { $('#{$name}').jstree({
  'core' : {
    'data' : {
      'url' : '{$dataUrl}',
      'dataType' : 'json',
      'data' : function (node) {
          console.log(node);
          if(node.id != '#')
              {
                  return { 'group_id' : node.id };
              }
          else
              {return;}
        
      }
    }
  }
}); });

$('#{$name}').on("select_node.jstree", function (e, data) {
window.location = data.node.a_attr.href;
});
</script>
EOT;

        return implode(PHP_EOL, $html);
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getTreeMenuDataProvider(): TreeMenuDataProvider
    {
        return $this->treeMenuDataProvider;
    }
}