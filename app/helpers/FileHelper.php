<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Application\BadRequestException;
use Nette\InvalidArgumentException;
use Nette\IOException;
use Nette\Utils\Random;
use Nette\Http\FileUpload;

class FileHelper
{
  const FILE_MIME_TYPES = [
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/epub+zip',
    'application/zip',
    'application/x-rar-compressed',
    'application/x-tar',
    'application/rtf',
    'application/pdf',
    'text/plain',
    'audio/wav',
    'audio/mpeg'
  ];

  const FILE_NAME_LENGTH = 21;
  const FILE_FOLDER = 'files';

  /**
   * @param array $files
   * @return array
   */
  public static function uploadFiles(array $files): array
  {
    $names = [];
    foreach ($files as $file) {
      if (!$file->isOk()) {
        throw new InvalidArgumentException;
      }

      $fileType = $file->getContentType();

      if (!in_array($fileType, self::FILE_MIME_TYPES)) {
        throw new InvalidArgumentException;
      }

      $baseName = $file->getSanitizedName();
      $pathInfo = pathinfo($baseName);
      $extension = strtolower($pathInfo['extension']);
      $fileName = $pathInfo['filename'];
      $newName = $fileName . '.' . $extension;

      if (!$file->move(self::FILE_FOLDER . '/' . $newName)) {
        throw new IOException;
      }

      $names[] = [ 'title' => $fileName, 'base_name' => $baseName ];
    }
    return $names;
  }

  /**
   * @param array $files
   * @return array
   */
  public static function uploadFile(FileUpload $upload)
  {
    if (!$upload->hasFile()) {
      return null;
    }

    if (!$upload->isOk()) {
      throw new InvalidArgumentException;
    }

    $names = [];

    $fileType = $upload->getContentType();

    if (!in_array($fileType, self::FILE_MIME_TYPES)) {
      throw new InvalidArgumentException;
    }

    $baseName = $upload->getSanitizedName();
    $pathInfo = pathinfo($baseName);
    $extension = strtolower($pathInfo['extension']);
    $newName = $pathInfo['filename'] . '.' . $extension;

    if (!$upload->move(self::FILE_FOLDER . '/' . $newName)) {
      throw new IOException;
    }

    return [ 'title' => $pathInfo['filename'], 'file_name' => $newName ];
  }
}