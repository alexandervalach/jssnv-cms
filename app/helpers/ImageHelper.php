<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Nette\IOException;
use Nette\SmartObject;
use Nette\Utils\Random;

/**
 * Class ImageHelper
 * @package App\Helpers
 */
class ImageHelper
{
  use SmartObject;

  /**
   *
   */
  const IMAGE_FOLDER = 'images';
  const IMAGE_NAME_LENGTH = 16;

  /**
   * @param FileUpload $image
   * @return string
   */
  public static function uploadImage(FileUpload $image): string
  {
    if (!$image->isOk() || (!$image->isImage() && $image->getContentType() !== 'image/svg' && $image->getContentType() !== 'image/svg+xml')) {
      throw new InvalidArgumentException;
    }

    $imageName = strtolower($image->getSanitizedName());
    $pathInfo = pathinfo($imageName);
    $extension = $pathInfo['extension'];
    $newName = Random::generate(self::IMAGE_NAME_LENGTH) . '.' . $extension;

    if (!$image->move(self::IMAGE_FOLDER . '/' . $newName)) {
      throw new IOException;
    }

    return $newName;
  }

  /**
   * @param array $images
   * @return array
   */
  public static function uploadImages(array $images): array
  {
    $names = [];
    foreach ($images as $image) {
      if (!$image->isOk() || !$image->isImage()) {
        throw new InvalidArgumentException;
      }

      $imageName = strtolower($image->getSanitizedName());
      $pathInfo = pathinfo($imageName);
      $extension = $pathInfo['extension'];
      $newName = Random::generate(self::IMAGE_NAME_LENGTH) . '.' . $extension;

      if (!$image->move(self::IMAGE_FOLDER . '/' . $newName)) {
        throw new IOException;
      }

      $names[] = $newName;
    }
    return $names;
  }
}