<?php
/**
 * Provide a visual test page for image tools 
 */
class ImagetoolstestController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		$ret = array();
		if (Config::has_feature(ConfigImageTools::IS_TEST_CONTROLLER_ENABLED)) {
			$ret = array(
				new ExactMatchRoute('imagetools/test/', $this, 'imagetools_test_index', new NoCacheCacheManager()),
				new ParameterizedRoute('imagetools/test/image-{type:e:src,resize,crop,watermark,fit}', $this, 'imagetools_test_image', new NoCacheCacheManager())
			);
		}
		return $ret;
	}

	/**
	 * Show test main page and handle upload
	 */
	public function action_imagetools_test_index(PageData $page_data) {
		$err = new Status();
		if ($page_data->has_post_data()) {
			$err->merge($this->do_upload($page_data));
		}
		
		$page_data->status = $err;
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'imagetools/test/index', $page_data);
		$view->render();
	}
	
	/**
	 * Upload file, and ally tools
	 */
	protected function do_upload(PageData $page_data) {
		$ret = new Status();
		$post_item = $page_data->get_post()->get_item('upload');
		$i_err = Arr::get_item($post_item, 'error', UPLOAD_ERR_NO_FILE);
		switch ($i_err) {
			case UPLOAD_ERR_OK:
				$tmp_file = Arr::get_item($post_item, 'tmp_name', '');
				//$org_file = Arr::get_item($post_item, 'name', '');
				$this->apply_tools($tmp_file);				
				break;		
			case UPLOAD_ERR_NO_FILE:
				$ret->append('No file was uploaded');
				break;				
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$ret->append('The uploaded file is too big');
				break;
			default:
				$ret->append('An unknown error code was retrieved while uploading the file.');
				break;
		}
		return $ret;		
	}
	
	protected function apply_tools($tmp_file) {
		$tmp_dir = Config::get_value(Config::TEMP_DIR);
		Load::components('imagetoolsfactory');
		$tools = ImageToolsFactory::create_imagetools();
		$src = $tools->create_from_file($tmp_file);
		$src->save_to_file($tmp_dir . 'imgtooltest-src');
		
		$resized = $tools->resize($src, 300, 100);
		$resized->save_to_file($tmp_dir . 'imgtooltest-resize');

		$resized = $tools->crop($src, ($src->get_width() - 100) / 2, ($src->get_height() - 100) / 2, 100, 100);
		$resized->save_to_file($tmp_dir . 'imgtooltest-crop');		
		
		$watermark = $tools->watermark($src);
		$watermark->save_to_file($tmp_dir . 'imgtooltest-watermark');

		$resized = $tools->fit($src, 300, 100);
		$resized->save_to_file($tmp_dir . 'imgtooltest-fit');
	}
	
	/**
	 * Show image
	 */
	public function action_imagetools_test_image(PageData $page_data, $type) {
		$page_data->page_template = 'emptypage';
		$basepath = Config::get_value(Config::TEMP_DIR) . 'imgtooltest-' . $type;
		foreach(array('jpg' => 'jpeg', 'png' => 'png', 'gif' => 'gif') as $ext => $mime) {
			$path = $basepath . '.' . $ext;
			if (file_exists($path)) {
				header('Content-Type: image/'. $mime);
				$page_data->content = file_get_contents($path);
			}
		}
		if (empty($page_data->content)) {
			return self::NOT_FOUND;
		}
	}
}