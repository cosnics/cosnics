<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Exception;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationConsulter
{
    protected CacheDataLoaderInterface $dataLoader;

    public function __construct(CacheDataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function getDataLoader(): CacheDataLoaderInterface
    {
        return $this->dataLoader;
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
        return $this->getDataLoader()->loadCachedData();
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
