<?php
const RT_MODULE_DIR = __DIR__;

function RaketaReadPathToArray($path)
{
	$arr = [];
	if ($handle = opendir($path)) {
		while (false !== ($entry = readdir($handle))) {
			if (
				$entry != "." && $entry != ".." && $entry !== 'index.php' && $entry !== 'OptionsInfo.php' &&
				(str_contains($entry, '.php') || !str_contains($entry, '.'))
			) {
				$arr[$entry] = RaketaReadPathToArray($path . '/' . $entry);
				if (is_array($arr[$entry]) && empty($arr[$entry])) {
					unset($arr[$entry]);
					$arr[] = $entry;
				}
			}
		}
		closedir($handle);
	}
	return $arr;
}

function computeFilePaths(array $fileTree): array
{
	$filePaths = [];
	$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($fileTree));
	foreach ($iterator as $fileName) {
		for ($folders = [], $pos = 0, $depth = $iterator->getDepth(); $pos < $depth; $pos++) {
			$folders[] = $iterator->getSubIterator($pos)->key();
		}
		$className = implode('/', $folders) . '/' . str_replace('.php', '', $fileName);
		$className = str_replace('/', '\\', $className);

		$filePaths['\\Wilp\\' . $className] = 'lib/' . implode('/', $folders) . '/' . $fileName;
		if (!str_contains($filePaths['\\Wilp\\' . $className], '.php')) {
			unset($filePaths['\\Wilp\\' . $className]);
		}
	}

	return $filePaths;
}

CModule::AddAutoloadClasses("wilpkron.bitrix_helper", computeFilePaths(RaketaReadPathToArray(__DIR__ . '/lib')));
