<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRendition;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class DummyBlockRenditionImplementation extends AbstractBlockRenditionImplementation
{

    /**
     *
     * @var string
     */
    private $format;

    /**
     *
     * @var string
     */
    private $view;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     * @param \Chamilo\Core\Reporting\ReportingBlock $block
     * @param string $format
     * @param string $view
     */
    public function __construct($context, ReportingBlock $block, $format, $view)
    {
        parent::__construct($context, $block);
        $this->format = $format;
        $this->view = $view;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return BlockRendition::launch($this);
    }

    /**
     * @return string
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function get_view()
    {
        return $this->view;
    }
}
