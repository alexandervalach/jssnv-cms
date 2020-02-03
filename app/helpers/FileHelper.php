<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\InvalidArgumentException;
use Nette\IOException;
use Nette\Utils\Random;

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

      $fileName = $file->getSanitizedName();
      $pathInfo = pathinfo($fileName);
      $extension = strtolower($pathInfo['extension']);
      $randomName = Random::generate(self::FILE_NAME_LENGTH) . '.' . $extension;

      if (!$file->move(self::FILE_FOLDER . '/' . $randomName)) {
        throw new IOException;
      }

      $names[] = [ 'desc' => $fileName, 'file_name' => $randomName ];
    }
    return $names;
  }
}