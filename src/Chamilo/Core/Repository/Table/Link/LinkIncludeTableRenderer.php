<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkIncludeTableRenderer extends LinkTableRenderer
{

    use LinkContentObjectTableRendererTrait;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        ContentObjectUrlGenerator $contentObjectUrlGenerator, StringUtilities $stringUtilities,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;
        $this->stringUtilities = $stringUtilities;
    }

}
