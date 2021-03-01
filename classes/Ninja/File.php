<?php

namespace Ninja;

use \Exception;

/**
 * Handle file actions
 * @package Ninja
 * @author Francesco Cesari
 * @version 0.1
 */

class File
{
    // path to file
    public $path;

    // basename of the file
    public $basename;

    // file extension
    public $extension;

    // file size in bytes
    public $size;

    // file real path
    public $realpath;

    // path to firectory where save temporary fles
    protected const TMP_PATH = __DIR__ . '/../../tmp/zip';

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->basename = basename($this->path);
        $this->extension = pathinfo($this->path, PATHINFO_EXTENSION);
        $this->size = filesize($this->path);
        $this->realpath = realpath($this->path);
    }

    public function is_file(): bool
    {
        return is_file($this->path);
    }

    public function is_dir(): bool
    {
        return is_dir($this->path);
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function save(string $new_filepath, array $valid_extensions = []): void
    {
        $ext = strtolower(pathinfo($new_filepath, PATHINFO_EXTENSION));
        $new_filepath = substr($new_filepath, 0, strrpos($new_filepath, '.')) . "." . $ext;
        if (!$this->exists()) {
            throw new Exception($this->path . ' not exists');
        }

        if (!empty($valid_extensions) && !in_array($ext, $valid_extensions)) {
            throw new Exception('Extension not allowed: ' . $this->path . ' ' . $this->size);
        }

        if ($this->size > $this->str2bytes(ini_get('upload_max_filesize'))) {
            throw new Exception('File size out of limit: ' . $this->path . ' ' . $this->size);
        }

        move_uploaded_file($this->path, $new_filepath);
    }

    public function download(): void
    {
        if (!$this->exists()) {
            throw new Exception($this->path . ' not exists');
        }

        if ($this->is_file()) {
            $this->download_file();
        } else if ($this->is_dir()) {
            $this->download_dir();
        }
    }

    private function download_file(): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; path="' . $this->basename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $this->size);
        flush();
        readfile($this->path);
        exit;
    }

    private function download_dir(): void
    {
        $zip_name = $this->zip(self::TMP_PATH);
        $this->download($zip_name);
        $this->delete($zip_name);
        exit;
    }

    public function delete(): void
    {
        if (!$this->exists()) {
            throw new Exception($this->path . ' not exists');
        }

        if ($this->is_file()) {
            unlink($this->path);
        } else if ($this->is_dir()) {
            $this->empty();
            rmdir($this->path);
            echo $this->path;
        }
    }

    public function empty(): void
    {
        if (!$this->exists()) {
            throw new Exception($this->path . ' not exists');
        }

        if (!$this->is_dir()) {
            throw new Exception($this->path . ' is not a dir');
        }

        $dir = substr($this->path, 0, strrpos($this->path, '/'));
        $files = glob($dir . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function is_empty(): bool
    {
        if (!$this->exists()) {
            throw new Exception($this->path . ' not exists');
        }

        $dir = substr($this->path, 0, strrpos($this->path, '/'));
        $files = glob($dir . '/*');
        return (empty($files));
    }

    /*public function zipFileList(string $path, array $files, string $path)
    {
        $zip = new \ZipArchive();

        $zip_name = $path . "$path.zip";

        if ($zip->open($zip_name, \ZIPARCHIVE::CREATE) !== TRUE) {
            exit("Impossibile creare il file zip");
        }

        foreach ($files as $file) {
            if (file_exists($file)) {
                $zip->addFile($file, basename($file));
            }
        }

        $zip->close();
        return $zip_name;
    }*/

    public function zip(string $path): string
    {
        $zip = new \ZipArchive();
        $zip_name = "$path/$this->basename.zip";
        $zip->open($zip_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $path => $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($path) + 1);
                $zip->addFile($file_path, $relative_path);
            }
        }
        $zip->close();
        return $zip_name;
    }

    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    public function size_convert(string $bytes): string
    {
        $bytes = floatval($bytes);
        $ar_bytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($ar_bytes as $ar_item) {
            if ($bytes >= $ar_item["VALUE"]) {
                $result = $bytes / $ar_item["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $ar_item["UNIT"];
                break;
            }
        }
        return $result;
    }

    /**
     * Convert number with unit byte to bytes unit
     * @link https://en.wikipedia.org/wiki/Metric_prefix
     * @param string $value a number of bytes with optinal SI decimal prefix (e.g. 7k, 5mb, 3GB or 1 Tb)
     * @return integer|float A number representation of the size in BYTES (can be 0). otherwise FALSE
     */
    function str2bytes($value)
    {
        // only string
        $unit_byte = preg_replace('/[^a-zA-Z]/', '', $value);
        $unit_byte = strtolower($unit_byte);
        // only number (allow decimal point)
        $num_val = preg_replace('/\D\.\D/', '', $value);
        switch ($unit_byte) {
            case 'p':    // petabyte
            case 'pb':
                $num_val *= 1024;
            case 't':    // terabyte
            case 'tb':
                $num_val *= 1024;
            case 'g':    // gigabyte
            case 'gb':
                $num_val *= 1024;
            case 'm':    // megabyte
            case 'mb':
                $num_val *= 1024;
            case 'k':    // kilobyte
            case 'kb':
                $num_val *= 1024;
            case 'b':    // byte
                return $num_val *= 1;
                break; // make sure
            default:
                return false;
        }
        return false;
    }
}
