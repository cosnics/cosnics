<?php
namespace Chamilo\Core\Repository\Common\Import\Youtube;

use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Libraries\File\Properties\FileProperties;

class YoutubeImportParameters extends ImportParameters
{
    const CLASS_NAME = __CLASS__;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $type
     * @param int $user
     * @param int $category
     * @param FileProperties $file
     * @param multitype:string $values
     */
    public function __construct($type, $user, $category, $file, $values)
    {
        parent :: __construct($type, $user, $category);
        $this->url = $values[YouTubeContentObjectImportForm :: PROPERTY_URL];
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }
}
