<?php

namespace Wilp\Helpers\Image;

class ImageResizeByInterventionImage
{
	public static $PUBLIC_DIR = '/upload/raketa_resize/';

	public static function clearImageConvectorIBlock($pathResizeDirServer)
	{
		if(!empty($pathResizeDirServer)) {
			$files = glob($pathResizeDirServer . '/*');
			foreach($files as $file) {
				if(is_file($file)) {
					unlink($file);
				}
			}
		}
	}

	public static function getImagePath($root, $image, $fileOption, $key)
	{
		return $root . '/' . $image['FILE_NAME'] . "_" . $fileOption["width"] . "x" . $fileOption["height"] . '_' . $key . '.' . $fileOption['extension'];
	}

	public static function getPathResizedDir($subDirImage)
	{
		return self::$PUBLIC_DIR . $subDirImage;
	}
	public static function getPathResizeDirServer($subDirImage)
	{
		return __DIR__ . '/../../../../../..' . self::getPathResizedDir($subDirImage);
	}


	public static function imageConvectorIBlock($imageIdent, $fileOptions) {
		$image = is_array($imageIdent) ? $imageIdent : \CFile::GetFileArray($imageIdent);
		$temp = [];

		$pathResizeDir = self::getPathResizedDir($image['SUBDIR']);
		$pathResizeDirServer = self::getPathResizeDirServer($image['SUBDIR']);
		if(is_dir($pathResizeDirServer)) {
			// foreach ($fileOptions as $key => $fileOption) {
			// 	$pathFile = self::getImagePath($pathResizeDirServer, $image, $fileOption, $key);
			// 	if(!is_file($pathFile)) {
			// 		self::clearImageConvectorIBlock($pathResizeDirServer);
			// 		break;
			// 	}
			// }
		} else {
			if (!mkdir($pathResizeDirServer, 0755, true)) {
				return;
			}
		}

		foreach ($fileOptions as $key => $fileOption) {
			$path = self::getImagePath($pathResizeDir, $image, $fileOption, $key);;
			if(
				is_file($_SERVER['DOCUMENT_ROOT'] . $image['SRC']) &&
				!is_file($_SERVER['DOCUMENT_ROOT'] . $path)
			) {
				try {
					\Intervention\Image\ImageManagerStatic::make(
						$_SERVER['DOCUMENT_ROOT'] . $image['SRC']
					)->fit(
						$fileOption["width"], $fileOption["height"]
					)->save(
						$_SERVER['DOCUMENT_ROOT'] . $path, 100, $fileOption["extension"]
					);
				} catch (Exception $e) {
					$path = $image['SRC'];
				}
			}
			if (is_file($_SERVER['DOCUMENT_ROOT'] . $image['SRC'])) {
				$temp[$key] = $path;
			}
		}
		return $temp;
	}

}
