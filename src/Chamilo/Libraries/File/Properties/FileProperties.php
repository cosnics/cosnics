<?php
namespace Chamilo\Libraries\File\Properties;

use finfo;

class FileProperties
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_EXTENSION = 'extension';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_PATH = 'path';

    /**
     *
     * @var multitype:string
     */
    private $properties;

    public function get_properties()
    {
        return $this->properties;
    }

    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    public function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

    public function get_property($property)
    {
        return $this->properties[$property];
    }

    public function get_name()
    {
        return $this->get_property(self :: PROPERTY_NAME);
    }

    public function set_name($name)
    {
        $this->set_property(self :: PROPERTY_NAME, $name);
    }

    public function get_extension()
    {
        return $this->get_property(self :: PROPERTY_EXTENSION);
    }

    public function set_extension($extension)
    {
        $this->set_property(self :: PROPERTY_EXTENSION, $extension);
    }

    public function get_name_extension()
    {
        return $this->get_name() . '.' . $this->get_extension();
    }

    public function get_type()
    {
        return $this->get_property(self :: PROPERTY_TYPE);
    }

    public function set_type($type)
    {
        $this->set_property(self :: PROPERTY_TYPE, $type);
    }

    public function get_size()
    {
        return $this->get_property(self :: PROPERTY_SIZE);
    }

    public function set_size($size)
    {
        $this->set_property(self :: PROPERTY_SIZE, $size);
    }

    public function get_path()
    {
        return $this->get_property(self :: PROPERTY_PATH);
    }

    public function set_path($path)
    {
        $this->set_property(self :: PROPERTY_PATH, $path);
    }

    public static function from_upload($file)
    {
        $file_name = $file['name'];
        $file_info = new finfo(FILEINFO_MIME_TYPE);

        $properties = new self();

        $file_name_parts = explode('.', $file_name);

        if (count($file_name_parts) >= 2)
        {
            $properties->set_extension(array_pop($file_name_parts));
        }

        $properties->set_name(implode('.', $file_name_parts));

        if (! $file_info)
        {
            $properties->set_type($file['type']);
        }
        else
        {
            $properties->set_type($file_info->file($file['tmp_name']));
        }

        $properties->set_size($file['size']);
        $properties->set_path($file['tmp_name']);

        return $properties;
    }

    public static function from_path($path)
    {
        $file_name = array_pop(explode(DIRECTORY_SEPARATOR, $path));
        $file_info = new finfo(FILEINFO_MIME_TYPE);

        $properties = new self();

        $file_name_parts = explode('.', $file_name);

        if (count($file_name_parts) >= 2)
        {
            $properties->set_extension(array_pop($file_name_parts));
        }

        $properties->set_name(implode('.', $file_name_parts));

        if (! $file_info)
        {
            $properties->set_type('application/octet-stream');
        }
        else
        {
            $properties->set_type($file_info->file($path));
        }

        $properties->set_size(filesize($path));
        $properties->set_path($path);

        return $properties;
    }

    public static function from_url($url)
    {
        $url_info = parse_url($url);

        $properties = new self();

        if (in_array($url_info['scheme'], array('http', 'ftp', 'https')))
        {
            $file_name = array_pop(explode('/', $url));
            $file_name_parts = explode('.', $file_name);

            if (count($file_name_parts) >= 1)
            {
                if (count($file_name_parts) >= 2)
                {
                    $properties->set_extension(array_pop($file_name_parts));
                    $properties->set_name(implode('.', $file_name_parts));
                }
                else
                {
                    $properties->set_name(implode('.', $file_name_parts));
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

            $valid_http = $url_info['scheme'] == 'http' && $info['http_code'] == 200;
            $valid_ftp = $url_info['scheme'] == 'ftp' && $info['http_code'] == 350;
            $valid_https = $url_info['scheme'] == 'https' && $info['http_code'] == 200;

            if ($valid_http || $valid_ftp || $valid_https)
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
