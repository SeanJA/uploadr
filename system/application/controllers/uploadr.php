<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class uploadr extends Controller {

	function uploadr() {
		$this->data['title'] = 'upload files';
		parent::MY_Controller();
	}

	function index() {
		$files = array();
		$files['files'] = array(
			'title' => 'Uploaded Files',
			'files' => get_filenames('./public/uploads/files'),
		);
		$this->data['upload_url'] = base_url() . 'public/uploads/';
		$this->data['files'] = $files;

		$this->load->view('uploadr/index', $this->data);
	}

	function delete() {
		if (!$_POST) {
			redirect('uploadr');
		}
		$file = $this->input->post('file');
		$folder = $this->input->post('folder');
		$valid_folders = array(
			'files',
		);
		if (in_array($folder, $valid_folders)) {
			//get rid of all of the slashes
			$file = str_replace(array('\\', '/'), '', $file);
			$path = FCPATH . 'public/uploads/' . $folder . '/' . $file;
			if (is_file($path)) {
				unlink($path);
			}
		}
		redirect('uploadr');
	}

	function upload($param1='') {

		//send back json headers
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		$validTypes = array('jpeg', 'jpg', 'pdf', 'png', 'gif', 'bmp');
		//determine what kind of upload we are getting from the client
		if (isset($param1)) {
			$class = 'UploadFileXhr';
			//codeigniter replaces the . with a _ argh, we can fix that by doing this:
			$filename = substr_replace($param1, '.', strrpos($param1, '_'), strlen('_'));
		} elseif (isset($_FILES['qqfile'])) {
			$class = 'UploadFileForm';
			$filename = $_FILES['qqfile']['name'];
		} else {
			$result = array('success' => FALSE, 'error' => 'No files were uploaded.');
		}

		if (empty($result)) {
			/**
			 * @var Uploader
			 */
			$file = new $class($validTypes);
			$result['success'] = $file->handleUpload('public/uploads/files/', $filename, true);
			//if we failed, get the error message
			if (!$result['success']) {
				$result['error'] = $file->getError();
			}
		}

		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

}

/**
 * @license GPL2
 * @version 0.9
 * @brief Base class for file uploads
 * <code>
 * //Only allow images to be uploaded
 * $validTypes = array('jpeg', 'png', 'jpg', 'gif', 'bmp');
 * //determine what kind of upload we are getting from the client
 * if (isset($_GET['qqfile'])) {
 * 	$file = new UploadFileXhr($validTypes);
 * } elseif (isset($_FILES['qqfile'])) {
 * 	$file = new UploadFileForm($validTypes);
 * } else {
 * 	$result = array('success' => FALSE, 'error'=> 'No files were uploaded.');
 * }
 *
 * if(empty($result)){
 * 	$result['success'] = $file->handleUpload('uploads/');
 * 	$result['error'] = $file->getError();
 * }
 * // to pass data through iframe you will need to encode all html tags
 * echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 * </code>
 */
abstract class Uploader {

	/**
	 * The valid extensions for this uploader (like jpeg, xml, bmp, xls...)
	 * @var array
	 */
	private $validExtensions = array();
	/**
	 * The maximum filesize allowed
	 * @var int
	 */
	private $maxFileSize = 10485760;
	/**
	 * The error that occured
	 * @var string
	 */
	private $error = '';
	private $uploaded_filename = null;

	/**
	 * @param array $validExtensions a list of valid extensions (like jpeg, xml, bmp, xls...)
	 * @param int $maxFileSize in bytes (default 10 megabytes)
	 */
	public function __construct(array $validExtensions = array(), $maxFileSize = 10485760) {
		$this->validExtensions = $validExtensions;
		if ((int) $maxFileSize !== $maxFileSize) {
			throw new Exception('$maxFileSize should be an integer.');
		}
		$this->maxFileSize = $maxFileSize;
	}

	/**
	 * Save the file to the specified path
	 * @param string $path The path to save the file to
	 * @return boolean TRUE on success
	 */
	abstract protected function save($path);

	/**
	 * Get the name of the uploaded file
	 * @return string
	 */
	private function getName() {
		return $this->uploaded_filename;
	}

	/**
	 * Get the size of the uploaded file (bytes)
	 * @return int
	 */
	abstract protected function getSize();

	/**
	 * Make sure that the file extension is valid
	 * @param string $ext The file's extension
	 */
	private function validateExtension($ext) {
		//make sure that the file has one of the allowed extensions
		if ($this->validExtensions && !in_array($ext, $this->validExtensions)) {
			$these = implode(', ', $this->validExtensions);
			if (count($this->validExtensions) > 1) {
				$this->error = 'File has an invalid extension, it should be one of ' . $these . '.';
			} else {
				$this->error = 'File has an invalid extension, it can only be a(n) ' . $these . '.';
			}
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Remove invalid characters from the file name (based on what windows says are bad characters to be safe)
	 * @param string $filename The filename without the extension
	 * @return string
	 */
	private function removeBadCharacters($filename) {
		$search = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
		$replace = '_';
		return str_replace($search, $replace, $filename);
	}

	/**
	 * Handle the uploading of the file
	 * @param string $uploadDirectory Where the files are
	 * @param boolean $replaceOldFile Whether or not to replace old files
	 * @return boolean TRUE on success, FALSE on failure
	 */
	public function handleUpload($uploadDirectory, $uploaded_filename, $replaceOldFile = FALSE) {
		$this->uploaded_filename = $uploaded_filename;
		if (!is_string($uploadDirectory)) {
			throw new Exception('$uploadDir should be a string.');
		}
		$uploadSize = $this->getSize();
		if ($uploadSize == 0) {
			$this->error = 'File is empty.';
			return FALSE;
		}
		if ($uploadSize > $this->maxFileSize) {
			$this->error = 'File is too large';
			return FALSE;
		}
		$uploaded = $this->getName();
		$this->removeBadCharacters($uploaded);
		$pathinfo = pathinfo($this->getName());
		$filename = $pathinfo['filename'];
		if (!isset($pathinfo['extension'])) {
			$pathinfo['extension'] = '';
		}
		$ext = $pathinfo['extension'];
		if (!$this->validateExtension($ext)) {
			return FALSE;
		}
		if (!$replaceOldFile) {
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		return $this->save($uploadDirectory . $filename . '.' . $ext);
	}

	public function getError() {
		return $this->error;
	}

}

/**
 * Handle file uploads via XMLHttpRequest
 * @link http://www.w3.org/TR/XMLHttpRequest/
 */
class UploadFileXhr Extends Uploader {

	/**
	 * Save the file to the specified path
	 * @param string $path
	 */
	protected function save($path) {
		$input = fopen("php://input", "r");
		$fp = @fopen($path, "w");
		if (!$fp) {
			$this->error = 'Could not save uploaded file.';
			return FALSE;
		}
		while ($data = fread($input, 1024)) {
			fwrite($fp, $data);
		}
		fclose($fp);
		fclose($input);
		return TRUE;
	}

	/**
	 * Get the size of the file
	 * @return int
	 */
	protected function getSize() {
		$headers = apache_request_headers();
		return (int) $headers['Content-Length'];
	}

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 * @link http://php.net/manual/en/reserved.variables.files.php
 */
class UploadFileForm Extends Uploader {

	/**
	 * Save the file to the specified path
	 * @param string $path
	 */
	protected function save($path) {
		if (!@move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
			$this->error = 'Could not save uploaded file.';
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Get the file size in bytes
	 * @return int
	 */
	protected function getSize() {
		return $_FILES['qqfile']['size'];
	}

}