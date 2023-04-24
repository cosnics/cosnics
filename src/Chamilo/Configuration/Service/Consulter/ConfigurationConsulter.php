<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Exception;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationConsulter
{
    protected CacheDataPreLoaderInterface $dataPreLoader;

    public function __construct(CacheDataPreLoaderInterface $dataPreLoader)
    {
        $this->dataPreLoader = $dataPreLoader;
    }

    public function getDataPreLoader(): CacheDataPreLoaderInterface
    {
        return $this->dataPreLoader;
    }

    /**
     * @param string[] $keys
     *
     * @return string|string[]
     */
    public function getSetting(array $keys)
    {
        try
        {
            $variables = $keys;
            $values = $this->getSettings();

            while (count($variables) > 0)
            {
                $key = array_shift($variables);

                if (!array_key_exists($key, $values))
                {
                    throw new Exception(
                        'The requested variable is not available in an unconfigured environment (' .
                        implode(' > ', $keys) . ')'
                    );
                }
                else
                {
                    $values = $values[$key];
                }
            }

            return $values;
        }
        catch (Exception $ex)
        {
            return null;
        }
    }

    /**
     * @return string[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getSettings(): array
    {
        return $this->getDataPreLoader()->preLoadCacheData();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function hasSettingsForContext(string $context): bool
    {
        $settings = $this->getSettings();

        return isset($settings[$context]);
    }
}
