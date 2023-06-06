<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Home\Renderer\Type\Basic
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BlockRendererFactory
{
    public const SOURCE_AJAX = 2;

    public const SOURCE_DEFAULT = 1;

    /**
     * The source from which this block renderer is called
     *
     * @var int
     */
    protected $source;

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * @var \Chamilo\Core\Home\Storage\DataClass\Block
     */
    private $block;

    /**
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param int $source
     */
    public function __construct(
        Application $application, HomeService $homeService, Block $block, $source = self::SOURCE_DEFAULT
    )
    {
        $this->application = $application;
        $this->homeService = $homeService;
        $this->block = $block;
        $this->source = $source;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    public function getHomeService()
    {
        return $this->homeService;
    }

    /**
     * @return \Chamilo\Core\Home\Renderer\BlockRenderer
     */
    public function getRenderer()
    {
        $block = $this->getBlock();
        $class = $block->getContext() . '\Integration\Chamilo\Core\Home\Type\\' . $block->getBlockType();

        return new $class($this->getApplication(), $this->getHomeService(), $block, $this->source);
    }

    /**
     * @return int
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     */
    public function setBlock(Block $block)
    {
        $this->block = $block;
    }

    /**
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     */
    public function setHomeService(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }
}
