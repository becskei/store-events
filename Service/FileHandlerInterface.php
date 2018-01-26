<?php

namespace Service;

interface FileHandlerInterface
{
    /**
     * @param string $uploadedFolder
     * @param string $extension
     *
     * @return bool
     */
    public function hasFiles($uploadedFolder, $extension);

    /**
     * @param string $uploadedFolder
     *
     * @return array
     */
    public function getFiles($uploadedFolder);

    /**
     * @param string $file
     *
     * @return array
     */
    public function csvToArray($file);

    /**
     * @param string $file
     * @param string $content
     * @param bool   $append
     */
    public function write($file, $content, $append = true);

    /**
     * @param string $from
     * @param string $to
     */
    public function move($from, $to);
}
