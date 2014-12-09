<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
ob_start();

$out = array();

if (!empty($_FILES)) {
	if (empty($_FILES['file_upload']['error'])) {
		$tempFile = $_FILES['file_upload']['tmp_name'];
		$targetPath = "audio/";
		$fileParts  = pathinfo($_FILES['file_upload']['name']);

		if ($fileParts['extension'] == "mp3" && $_FILES['file_upload']['type'] == 'audio/mp3') {
			$new_name = str_replace(array('.',' ','-', '/'),'', $fileParts['filename'])."-";
			$new_name .= str_replace(array('.',' ','-', '/'),'', uniqid(true));

			$targetFile = $targetPath . $new_name . "." . $fileParts['extension'];

			if (file_exists(__DIR__ . "/" . $targetFile)) {
				$out['error'] = "A file with that name already exists.";
			} else {
				if (move_uploaded_file($tempFile, __DIR__ . "/" . $targetFile)) {
					if (chmod(__DIR__ . "/" . $targetFile, 0644)) {
						$out['file_name'] = $targetFile;
					} else {
						$out['error'] = "Unable to set file permissions.";
					}
				} else {
					$out['error'] = "Unable to save file.";
				}
			}
		} else {
			$out['error'] = "Invalid file type.";
		}
	} else {
		switch ($_FILES['file_upload']['error']) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$out['error'] = "The file size is too large.";
				break;
			case UPLOAD_ERR_PARTIAL:
				$out['error'] = "The file was only partially uploaded.";
				break;
			case UPLOAD_ERR_NO_FILE:
				$out['error'] = "No file was uploaded.";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$out['error'] = "Missing a temporary folder.";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$out['error'] = "Failed to write to disc.";
				break;
			case UPLOAD_ERR_EXTENSION:
				$out['error'] = "An extension stopped the file upload.";
				break;
			default:
				$out['error'] = "There was an error uploading the file.";
				break;
		}
	}
} else {
	$out['error'] = "No file uploaded.";
}

$php_err = ob_get_clean();
if (!empty($php_err)) $out['php_err'] = $php_err;

echo json_encode($out);

die();