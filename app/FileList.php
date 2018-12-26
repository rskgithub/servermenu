<?php
	
namespace ServerMenu;

class FileList {
	
	public static function get()
	{
		
		$app = \Slim\Slim::getInstance();
		
		$dir = $app->config('s')['app']['download_dir'];
		$rdi = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
		$files = [];
		
		foreach(new \RecursiveIteratorIterator($rdi) as $file) {
            /** @var $file \SplFileInfo */
			$ok = true;
			foreach ($app->config('s')['app']['exclude_files'] as $ext) {
				if(Utility::endsWith($file, $ext)) {
			        $ok = false;
			    }
			}

			if ($ok) {
				$files[$file->getMTime().':'.$file->getRealPath()] = [
					'filename' => $file->getFilename(),
					'path' => $file->getPath(),
					'size' => Utility::bytes2human($file->getSize(), 2, false),
					'modified' => Utility::time2relative($file->getMTime())
				];
			}
		}
		
		ksort($files);
		
		return array_reverse($files);
	}
	
	
}
