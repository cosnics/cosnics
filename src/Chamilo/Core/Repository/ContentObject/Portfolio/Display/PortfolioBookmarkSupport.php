<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display;

/**
 * Interface that indicates an implementer supports bookmarking in portfolios
 * 
 * @package repository\content_object\portfolio\display$PortfolioBookmarkSupport
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface PortfolioBookmarkSupport
{

    /**
     * Get a pre-configured Bookmark object
     * 
     * @param int $current_step
     * @return \core\repository\content_object\bookmark\Bookmark
     */
    public function get_portfolio_bookmark($current_step);
}
