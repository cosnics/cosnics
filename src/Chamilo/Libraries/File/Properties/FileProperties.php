<?php
namespace Chamilo\Libraries\File\Properties;

use finfo;

/**
 *
 * @package Chamilo\Libraries\File\Properties
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FileProperties
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_EXTENSION = 'extension';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_PATH = 'path';

    /**
     *
     * @var string[]
     */
    private $properties;

    /**
     *
     * @return string[]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     *
     * @param string[] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     *
     * @param string $property
     * @param string $value
     */
    public function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     *
     * @param string $property
     * @return string
     */
    public function get_property($property)
    {
        return $this->properties[$property];
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_property(self::PROPERTY_NAME);
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_property(self::PROPERTY_NAME, $name);
    }

    /**
     *
     * @return string
     */
    public function get_extension()
    {
        return $this->get_property(self::PROPERTY_EXTENSION);
    }

    /**
     *
     * @param string $extension
     */
    public function set_extension($extension)
    {
        $this->set_property(self::PROPERTY_EXTENSION, $extension);
    }

    /**
     *
     * @return string
     */
    public function get_name_extension()
    {
        return $this->get_name() . '.' . $this->get_extension();
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->get_property(self::PROPERTY_TYPE);
    }

    /**
     *
     * @param string $type
     */
    public function set_type($type)
    {
        $this->set_property(self::PROPERTY_TYPE, $type);
    }

    /**
     *
     * @return integer
     */
    public function get_size()
    {
        return $this->get_property(self::PROPERTY_SIZE);
    }

    /**
     *
     * @param integer $size
     */
    public function set_size($size)
    {
        $this->set_property(self::PROPERTY_SIZE, $size);
    }

    /**
     *
     * @return string
     */
    public function get_path()
    {
        return $this->get_property(self::PROPERTY_PATH);
    }

    /**
     *
     * @param string $path
     */
    public function set_path($path)
    {
        $this->set_property(self::PROPERTY_PATH, $path);
    }

    /**
     *
     * @param string[] $fileDescription
     * @return \Chamilo\Libraries\File\Properties\FileProperties
     */
    public static function from_upload($fileDescription)
    {
        $fileName = $fileDescription['name'];
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

        $properties = new self();

        $fileNameParts = explode('.', $fileName);

        if (count($fileNameParts) >= 2)
        {
            $properties->set_extension(array_pop($fileNameParts));
        }

        $properties->set_name(implode('.', $fileNameParts));

        if (! $fileInfo)
        {
            $properties->set_type($fileDescription['type']);
        }
        else
        {
            $properties->set_type($fileInfo->file($fileDescription['tmp_name']));
        }

        $properties->set_size($fileDescription['size']);
        $properties->set_path($fileDescription['tmp_name']);

        return $properties;
    }

    /**
     *
     * @param string $path
     * @return \Chamilo\Libraries\File\Properties\FileProperties
     */
    public static function from_path($path)
    {
        $fileName = array_pop(explode(DIRECTORY_SEPARATOR, $path));
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

        $properties = new self();

        $fileNameParts = explode('.', $fileName);

        if (count($fileNameParts) >= 2)
        {
            $properties->set_extension(array_pop($fileNameParts));
        }

        $properties->set_name(implode('.', $fileNameParts));

        if (! $fileInfo)
        {
            $properties->set_type('application/octet-stream');
        }
        else
        {
            $properties->set_type($fileInfo->file($path));
        }

        $properties->set_size(filesize($path));
        $properties->set_path($path);

        return $properties;
    }

    /**
     *
     * @param string $url
     * @return \Chamilo\Libraries\File\Properties\FileProperties
     */
    public static function from_url($url)
    {
        $urlInfo = parse_url($url);

        $properties = new self();

        if (in_array($urlInfo['scheme'], array('http', 'ftp', 'https')))
        {
            $fileName = array_pop(explode('/', $url));
            $fileNameParts = explode('.', $fileName);

            if (count($fileNameParts) >= 1)
            {
                if (count($fileNameParts) >= 2)
                {
                    $properties->set_extension(array_pop($fileNameParts));
                    $properties->set_name(implode('.', $fileNameParts));
                }
                else
                {
                    $properties->set_name(implode('.', $fileNameParts));
                    $properties->set_extension('htm');
                }
            }
            else
            {
                $properties->set_name('index');
                $properties->set_extension('htm');
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_FILETIME, true);
            curl_setopt($curl, CURLOPT_NOBODY, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            $header = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);

            $validHttp = $urlInfo['scheme'] == 'http' && $info['http_code'] == 200;
            $validFtp = $urlInfo['scheme'] == 'ftp' && $info['http_code'] == 350;
            $validHttps = $urlInfo['scheme'] == 'https' && $info['http_code'] == 200;

            if ($validHttp || $validFtp || $validHttps)
            {
                if (! is_null($info['content_type']))
                {
                    $properties->set_type($info['content_type']);
                }
                else
                {
                    $properties->set_type('application/octet-stream');
                }

                $properties->set_size((int) $info['download_content_length']);
                $properties->set_path($url);
            }
        }

        return $properties;
    }
}
