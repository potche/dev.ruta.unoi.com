<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 30/05/16
 * Time: 12:53 PM
 */

namespace UNO\EvaluacionesBundle\Controller\FileUpload;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Class Document
 * @package UNO\EvaluacionesBundle\Controller\FileUpload
 */
class Document
{
    /** @var File  - not a persistent field! */
    private $file;

    /** @var string
     * @Column(type="string")
     */
    private $filePersistencePath;

    /** @var string */
    protected static $uploadDirectory = null;

    /** @var string */
    protected static $uploadHash = null;

    static public function setUploadDirectory($dir)
    {
        self::$uploadDirectory = $dir;
    }

    static public function getUploadDirectory()
    {
        if (self::$uploadDirectory === null) {
            throw new \RuntimeException("Trying to access upload directory for profile files");
        }
        return self::$uploadDirectory;
    }
    
    static public function setUploadHash($hash)
    {
        self::$uploadHash = $hash;
    }

    static public function getUploadHash()
    {
        return self::$uploadHash;
    }

    /**
     * Assumes 'type' => 'file'
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return new File(self::getUploadDirectory() . "/" . $this->filePersistencePath);
    }

    public function getFilePersistencePath()
    {
        return $this->filePersistencePath;
    }

    public function processFile()
    {
        if (! ($this->file instanceof UploadedFile) ) {
            return false;
        }
        $uploadFileMover = new UploadFileMover();
        $this->filePersistencePath = $uploadFileMover->moveUploadedFile($this->file, self::getUploadDirectory(), self::getUploadHash());
    }
}