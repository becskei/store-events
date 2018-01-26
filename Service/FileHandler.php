<?php

namespace Service;

class FileHandler implements FileHandlerInterface
{
    /**
     * @param string $uploadedFolder
     * @param string $extension
     *
     * @return bool
     */
    public function hasFiles($uploadedFolder, $extension)
    {
        if (!is_dir($uploadedFolder)) {
            return false;
        }

        $dirContents = scandir($uploadedFolder, SCANDIR_SORT_NONE);

        $files = [];
        foreach ($dirContents as $content) {
            if (pathinfo($content, PATHINFO_EXTENSION) === $extension) {
                $files[] = $content;
            }
        }

        return count($files) > 0;
    }

    /**
     * @param string $uploadedFolder
     *
     * @return array
     */
    public function getFiles($uploadedFolder)
    {
        $files = scandir($uploadedFolder, SCANDIR_SORT_NONE);

        $uploadedFiles = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $uploadedFiles[] = $uploadedFolder . '/' . $file;
        }

        return $uploadedFiles;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function csvToArray($file)
    {
        $array = [];
        $fileLines = file($file);
        foreach ($fileLines as $line) {
            $array[] = str_getcsv($line);
        }

        return $array;
    }

    /**
     * @param string $file
     * @param string $content
     * @param bool   $append
     */
    public function write($file, $content, $append = true)
    {
        file_put_contents($file, $content, $append ? FILE_APPEND : '');
    }

    /**
     * @param string $from
     * @param string $to
     */
    public function move($from, $to)
    {
        rename($from, $to);
    }
}
