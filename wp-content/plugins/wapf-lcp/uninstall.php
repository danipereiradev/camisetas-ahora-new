<?php

function delete_folder_and_contents($directory, $empty = false) {

	if(substr($directory,-1) == "/") {
		$directory = substr($directory,0,-1);
	}

	if(!file_exists($directory) || !is_dir($directory)) {
		return false;
	} elseif(!is_readable($directory)) {
		return false;
	} else {

		$directory_handle = opendir($directory);

		while ($contents = readdir($directory_handle)) {
			if($contents != '.' && $contents != '..') {
				$path = $directory . "/" . $contents;

				if(is_dir($path)) {
					delete_folder_and_contents($path);
				} else {
					unlink($path);
				}
			}
		}

		closedir($directory_handle);

		if($empty == false) {
			if(!rmdir($directory)) {
				return false;
			}
		}

		return true;
	}
}

if( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	delete_option('wapf_lcp_fonts');
	$upload_path = wp_upload_dir();
	$dir = $upload_path['basedir'] . '/wapf-lcp';
	delete_folder_and_contents( $dir );
}