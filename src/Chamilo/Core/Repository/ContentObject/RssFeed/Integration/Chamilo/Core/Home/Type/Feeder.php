<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\RenditionImplementation;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class Feeder extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
{

    /**
     * Returns the list of type names that this block can map to.
     * 
     * @return array
     */
    public static function get_supported_types()
    {
        $result = array();
        $result[] = RssFeed :: get_type_name();
        return $result;
    }

    public function __construct($parent, $block_info, $configuration)
    {
        parent :: __construct($parent, $block_info, $configuration);
        $this->default_title = Translation :: get('Feeder');
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Returns the html to display when the block is configured.
     * 
     * @return string
     */
    public function display_content()
    {
        $content_object = $this->get_object();
        
        $html = array();
        $feed = RenditionImplementation :: parse_file($content_object->get_url());
        if ($feed)
        {
            $target = $this->get_link_target();
            $target = $target ? 'target="' . $target . '"' : 'target="_blank"';
            $icon = Theme :: getInstance()->getImagesPath('\core\repository\content_object\rss_feed') . 'Logo/' .
                 Theme :: ICON_MINI . '.png';
            $html[] = '<div class="tool_menu">';
            $html[] = '<ul>';
            
            $count_valid = 0;
            
            foreach ($feed['items'] as $item)
            {
                if (! $item['link'] || ! $item['title'])
                {
                    continue;
                }
                
                $count_valid ++;
                
                $html[] = '<li class="tool_list_menu" style="background-image: url(' . $icon . ')"><a href="' . htmlentities(
                    $item['link']) . '" ' . $target . '>' . $item['title'] . '</a></li>';
            }
            
            $html[] = '</ul>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        if (! $feed || $count_valid == 0)
        {
            $html[] = '<span style="font-weight: bold;">' . Translation :: get('NoFeedsFound') . '</span>';
        }
        
        return '<div style="height: 4px;"></div>' . implode(PHP_EOL, $html);
    }
}
