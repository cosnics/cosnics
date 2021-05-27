<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

/**
 *
 * @package Chamilo\Core\Repository\Implementation\Youtube
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PageTokenGenerator
{
    const PREFIX = 'C';
    const POSTFIX = 'AA';

    /**
     *
     * @var \Chamilo\Core\Repository\Implementation\Youtube\PageTokenGenerator
     */
    private static $instance;

    /**
     *
     * @var string[]
     */
    private static $characterMap;

    /**
     *
     * @var string[]
     */
    private $pageTokenCache;

    /**
     *
     * @return \Chamilo\Core\Repository\Implementation\Youtube\PageTokenGenerator
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static();
        }
        
        return static::$instance;
    }

    public function __construct()
    {
        self::$characterMap = array_merge(range("A", "Z", 4), range("c", "z", 4), range(0, 9, 4));
        $this->pageTokenCache = [];
    }

    /**
     *
     * @param integer $maximumNumberOfResults
     * @param integer $pageNumber
     * @return string
     */
    public function getToken($maximumNumberOfResults, $pageNumber)
    {
        if (! isset($this->pageTokenCache[$maximumNumberOfResults]) ||
             ! isset($this->pageTokenCache[$maximumNumberOfResults][$pageNumber]))
        {
            if (($maximumNumberOfResults * ($pageNumber - 1)) >= 500)
            {
                $this->pageTokenCache[$maximumNumberOfResults][$pageNumber] = '';
            }
            else
            {
                $tokenParts = [];
                
                // The first character of the token is always the same
                $tokenParts[] = self::PREFIX;
                
                // Determine the start index of the first video to return
                $startIndex = 1 + ($pageNumber - 1) * $maximumNumberOfResults;
                
                /*
                 * Determine the first character.
                 * It is basically the startIndex divided by 16 and rounded down. The value is corrected for value 16
                 * (the border) and to never be higher then 15. The first run starts from A and continues to P.
                 * Consecutive loops restart from I to P.
                 */
                $floor = floor($startIndex / 16);
                $floor = ($startIndex % 16) == 0 ? $floor - 1 : $floor;
                
                if ($floor >= 16)
                {
                    $floor = $floor - 8;
                    
                    if ($floor >= 16)
                    {
                        $floor = $floor - 8;
                    }
                }
                
                $tokenParts[] = chr(ord('A') + $floor);
                
                /*
                 * Determine the second character
                 * The characterMapIndex is determined by taking the moduls from the startIndex by 16 and once again
                 * correcting the border-value 16. Subtract one from the resulting index for the 0-based index system of
                 * the characterMap. The corresponding character is then added from the characterMap.
                 */
                $characterMapIndex = ($startIndex % 16);
                $characterMapIndex = $characterMapIndex == 0 ? 16 : $characterMapIndex;
                $characterMapIndex = $characterMapIndex - 1;
                
                $tokenParts[] = self::$characterMap[$characterMapIndex];
                
                /*
                 * Determine the extra character, if it is required.
                 * For each startIndex greater then 128 an extra character is added to the pageToken. It is basically
                 * the startIndex divided by 128 and rounded down. The value is corrected for value 128 (the border) and
                 * to never be higher then 127. Consecutive loops restart from I to P. If the startIndex is bigger then
                 * 128 an addition E is added, if not a Q is added.
                 */
                
                $floor = floor($startIndex / 128);
                $floor = ($startIndex % 128) == 0 ? $floor - 1 : $floor;
                
                if ($startIndex > 128)
                {
                    $tokenParts[] = chr(ord('A') + $floor);
                    $tokenParts[] = 'E';
                }
                else
                {
                    $tokenParts[] = 'Q';
                }
                
                // The last two characters of the token are always the same
                $tokenParts[] = self::POSTFIX;
                
                $this->pageTokenCache[$maximumNumberOfResults][$pageNumber] = implode('', $tokenParts);
            }
        }
        
        return $this->pageTokenCache[$maximumNumberOfResults][$pageNumber];
    }
}