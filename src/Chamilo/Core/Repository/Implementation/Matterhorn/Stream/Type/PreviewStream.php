<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Type;

use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Stream;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PreviewStream extends Stream
{

    /**
     *
     * @var \core\repository\implementation\matterhorn\Attachment
     */
    private $preview;

    /**
     *
     * @return \core\repository\implementation\matterhorn\Attachment
     */
    public function get_preview()
    {
        if (! isset($preview))
        {
            $this->preview = $this->get_external_object()->get_search_preview();
        }
        
        return $this->preview;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->get_preview()->get_url();
    }

    /**
     *
     * @return string
     */
    public function get_mimetype()
    {
        return $this->get_preview()->get_mimetype();
    }
}