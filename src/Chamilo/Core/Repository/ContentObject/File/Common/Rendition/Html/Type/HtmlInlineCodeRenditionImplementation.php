<?php

namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Path;

/**
 * Class HtmlInlineCodeRenditionImplementation
 */
class HtmlInlineCodeRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = [];

        /**
         * @var File $file
         */
        $file = $this->get_content_object();
        $html[] = '<pre><code>';
        $html[] = file_get_contents(Path::getInstance()->getRepositoryPath() . $file->get_path());
        $html[] = '</code></pre>';
        $html[] = $this->renderActions(['btn-info']);

        return implode(PHP_EOL, $html);
    }
}
