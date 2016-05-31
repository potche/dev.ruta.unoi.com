<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 30/05/16
 * Time: 12:55 PM
 */
namespace UNO\EvaluacionesBundle\Controller\FileUpload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Class UploadFileMover
 * @package UNO\EvaluacionesBundle\Controller\FileUpload
 */
class UploadFileMover
{
    public function moveUploadedFile(UploadedFile $file, $uploadBasePath, $nameHash = null)
    {
        $originalName = $file->getClientOriginalName();

        // use filemtime() to have a more determenistic way to determine the subpath, otherwise its hard to test.
        $relativePath = date('Y-m', filemtime($file->getPath()));
        //$targetFileName = $relativePath . DIRECTORY_SEPARATOR . $originalName;
        //$targetFilePath = $uploadBasePath . DIRECTORY_SEPARATOR . $targetFileName;
        $targetFilePath = $uploadBasePath . DIRECTORY_SEPARATOR . $originalName;
        $ext = $file->getExtension();

        $i = 1;
        while (file_exists($targetFilePath) && md5_file($file->getPath()) != md5_file($targetFilePath)) {
            if ($ext) {
                $prev = $i==1 ? "" : $i;
                $targetFilePath = $targetFilePath . str_replace($prev . $ext, $i++ . $ext, $targetFilePath);
            } else {
                $targetFilePath = $targetFilePath . $i++;
            }
        }

        //$targetDir = $uploadBasePath . DIRECTORY_SEPARATOR . $relativePath;
        $targetDir = $uploadBasePath;
        if (!is_dir($targetDir)) {
            $ret = mkdir($targetDir, umask(), true);
            if (!$ret) {
                throw new \RuntimeException("Could not create target directory to move temporary file into.");
            }
        }

        print_r($nameHash);
        $file->move($targetDir, $nameHash == null ? basename($targetFilePath) : $nameHash);

        return str_replace($uploadBasePath . DIRECTORY_SEPARATOR, "", $targetFilePath);
    }
}