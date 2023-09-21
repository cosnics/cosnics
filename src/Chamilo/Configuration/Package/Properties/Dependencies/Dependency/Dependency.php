<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies\Dependency
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Dependency
{
    public const PROPERTY_ID = 'id';
    public const PROPERTY_VERSION = 'version';

    public const TYPE_EXTENSIONS = 'extensions';
    public const TYPE_PACKAGE = 'package';
    public const TYPE_SERVER = 'server';
    public const TYPE_SETTINGS = 'settings';

    protected string $id;

    protected string $version;

    public function __construct(string $id, string $version)
    {
        $this->id = $id;
        $this->version = $version;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function get_version(): string
    {
        return $this->version;
    }

    public function set_id(string $id): void
    {
        $this->id = $id;
    }

    public function set_version(string $version): void
    {
        $this->version = $version;
    }
}
