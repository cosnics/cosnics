<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation;

use Chamilo\Core\Reporting\ReportingBlock;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class AbstractBlockRenditionImplementation
{

    /**
     *
     * @var \libraries\architecture\application\Application
     */
    private $context;

    /**
     *
     * @var \core\reporting\ReportingBlock
     */
    private $block;

    /**
     *
     * @param \libraries\architecture\application\Application $context
     * @param ReportingBlock $block
     */
    public function __construct($context, ReportingBlock $block)
    {
        $this->context = $context;
        $this->block = $block;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param \libraries\architecture\application\Application $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \core\reporting\ReportingBlock
     */
    public function get_block()
    {
        return $this->block;
    }

    /**
     *
     * @param ReportingBlock $block
     */
    public function set_block(ReportingBlock $block)
    {
        $this->block = $block;
    }

    /**
     *
     * @return string
     */
    abstract public function get_view();

    /**
     *
     * @return string
     */
    abstract public function get_format();
}
