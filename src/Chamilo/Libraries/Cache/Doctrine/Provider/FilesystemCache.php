<?php
namespace Chamilo\Libraries\Cache\Doctrine\Provider;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine\Provider
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FilesystemCache extends \Doctrine\Common\Cache\FilesystemCache
{

    /**
     *
     * @var string[] regular expressions for replacing disallowed characters in file name
     */
    private $disallowedCharacterPatterns = array('/\-/', '/[^a-zA-Z0-9\-_\[\]]/');

    /**
     *
     * @var string[] replacements for disallowed file characters
     */
    private $replacementCharacters = array('__', '-');

    /**
     *
     * @param string $id
     *
     * @return string
     */
    protected function getFilename($id)
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . md5($id) . DIRECTORY_SEPARATOR .
            preg_replace($this->disallowedCharacterPatterns, $this->replacementCharacters, $id) . $this->getExtension();
    }
}
