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
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $context;

    /**
     *
     * @var \Chamilo\Core\Reporting\ReportingBlock
     */
    private $block;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     * @param ReportingBlock $block
     */
    public function __construct($context, ReportingBlock $block)
    {
        $this->context = $context;
        $this->block = $block;
    }

    /**
     *
     * @return \Chamilo\Core\Reporting\ReportingBlock
     */
    public function get_block()
    {
        return $this->block;
    }

    /**
     *
     * @param \Chamilo\Core\Reporting\ReportingBlock $block
     */
    public function set_block(ReportingBlock $block)
    {
        $this->block = $block;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    abstract public function get_format();

    /**
     *
     * @return string
     */
    abstract public function get_view();
}
