<?php

	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 	
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 

	function sanitize_wpdocs_data( $input ) {
	
			if(is_array($input)){
			
				$new_input = array();
		
				foreach ( $input as $key => $val ) {
					$new_input[ $key ] = (is_array($val)?sanitize_wpdocs_data($val):sanitize_text_field( $val ));
				}
				
			}else{
				$new_input = sanitize_text_field($input);
			}
	
			if(!is_array($new_input)){
	
				if(stripos($new_input, '@') && is_email($new_input)){
					$new_input = sanitize_email($new_input);
				}
	
				if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
					$new_input = esc_url($new_input);
				}
	
			}
	
			
			return $new_input;
		}	
	
	function wpdocs_admin_enqueue_script()
	{
		if (isset($_GET['page']) && $_GET['page'] == 'wpdocs') {
			
			global $wpdocs_pro;
				
			wp_enqueue_script('wpdocs_boostrap', plugin_dir_url(dirname(__FILE__)) . 'js/bootstrap.min.js', array('jquery'));
			wp_enqueue_style('wpdocs-boostrap', plugins_url('css/bootstrap.min.css', dirname(__FILE__)));
	
			
			wp_enqueue_style('wpdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
	
			wp_enqueue_media();
	
			wp_enqueue_style('wpdocs-common', plugins_url('css/common-styles.css', dirname(__FILE__)));
			wp_enqueue_style('wpdocs-admin', plugins_url('css/admin-styles.css', dirname(__FILE__)));
	
			wp_enqueue_script('wpdocs_admin_scripts', plugin_dir_url(dirname(__FILE__)) . 'js/admin-scripts.js?t='.time(), array('jquery'));
			
			if($wpdocs_pro){
				wp_enqueue_script('wpdocs_pro_scripts', plugin_dir_url(dirname(__FILE__)) . 'pro/wp-docs-admin.js?t='.time(), array('jquery'));
			}
			
			
			wp_localize_script(
				'wpdocs_admin_scripts',
				'wpdocs_ajax_object',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'url' => admin_url('options-general.php?page=wpdocs'),
					'wpdocs_delete_msg' => __('Do you want to delete this directory and data as well?', 'wp-docs'),
					'target_dir_msg' => __('Select a target directory.', 'wp-docs'),
					'move_error' => __('Sorry! File could not move, please try again.', 'wp-docs'),
					'del_confirm' => __('Do you want to delete this file?', 'wp-docs'),
					'rename_confirm' => __('Do you want to rename this directory?', 'wp-docs'),
					'nonce' => wp_create_nonce('wpdocs_update_options_nonce'),
				)
			);
		}
	}
	
	add_filter( 'ajax_query_attachments_args', 'wpdocs_filter_media');
	function wpdocs_filter_media( $query ) {
		// admins get to see everything
		if ( ! current_user_can( 'manage_options' ) )
			$query['author'] = get_current_user_id();
	
		return $query;
	}
	
	add_action('admin_enqueue_scripts', 'wpdocs_admin_enqueue_script');
	
	add_action('wp_enqueue_scripts', 'wpdocs_wp_enqueue_script');
	
	function wpdocs_wp_enqueue_script()
	{
		global $post, $wpdocs_pro, $wpdocs_url, $wpdocs_options;
	
		$wpdocs_relevant_page = false;
		$localize_handler = 'wpdocs_front_scripts';
		//pree($post->post_content);
		//pree(stripos($post->post_content, '[wpdocs]'));
		if(!empty($post) && isset($post->post_content) && stripos(' '.$post->post_content, '[wpdocs')){
			$wpdocs_relevant_page = true;
		}
		//pree($wpdocs_relevant_page);
		if($wpdocs_relevant_page){
			
			$is_bootstrap = array_key_exists('bootstrap', $wpdocs_options);
			$is_file_upload = array_key_exists('file_upload', $wpdocs_options);
	
			if(is_admin() || ($wpdocs_pro && $is_file_upload && current_user_can( 'upload_files' ))){
	
				wp_enqueue_media();
	
			}
	
			if($is_bootstrap){ 		
				wp_enqueue_script('wpdocs_boostrap', plugin_dir_url(dirname(__FILE__)) . 'js/bootstrap.min.js');
				wp_enqueue_style('wpdocs-boostrap', plugins_url('css/bootstrap.min.css', dirname(__FILE__)));
			}
	
			$is_ajax_url = false;
			$is_ajax = false;
	
	
			if($wpdocs_pro){
	
				$is_ajax = array_key_exists('ajax', $wpdocs_options);
				$is_ajax_url = array_key_exists('ajax_url', $wpdocs_options);
	
				wp_enqueue_script('wpdocs_pro_scripts', $wpdocs_url . 'pro/wp-docs-pro.js?t='.time(), array('jquery'));
				$localize_handler = 'wpdocs_pro_scripts';
	
			}
	
			wp_enqueue_script('wpdocs_front_scripts', plugin_dir_url(dirname(__FILE__)) . 'js/front-scripts.js', array('jquery'));
			wp_enqueue_style('wpdocs-common', plugins_url('css/common-styles.css', dirname(__FILE__)));
			wp_enqueue_style('wpdocs-front', plugins_url('css/front-styles.css', dirname(__FILE__)));
		
			wp_enqueue_style('wpdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
	
			wp_localize_script(
				$localize_handler,
				'wpdocs',
				array(
				
					'wpdocs_pro' => $wpdocs_pro,
					'ajax_url' => admin_url('admin-ajax.php'),
					'this_url' => get_permalink(),
					'is_ajax' => $is_ajax,
					'is_ajax_url' => $is_ajax_url,
					'nonce' => wp_create_nonce('wpdocs_update_options_nonce'),
				)
			);
	
			
		}
	}
	
	if (is_admin()) {
		add_action('admin_menu', 'wpdocs_menu');
	}
	function wpdocs_menu()
	{
		global $wpdocs_data, $wpdocs_pro;
	
		$title = $wpdocs_data['Name'] . ' ' . ($wpdocs_pro ? ' ' . __('Pro', 'wp-docs') : '');
	
		add_options_page($title, $title, 'activate_plugins', 'wpdocs', 'wpdocs_settings');
	}
	function wpdocs_settings()
	{
		global $wpdocs_premium_link, $wpdocs_pro, $wpdocs_url;
		$wpdocs_options = get_option('wpdocs_options', array());
		$wpdocs_options = is_array($wpdocs_options)?$wpdocs_options:array();
		include_once('wpdocs_settings.php');
	}
	
	function wpdocs_list($post_parent = 0)
	{
		$ret = array();
	
		if (is_numeric($post_parent)) {
			$args = array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'title',
				'order'            => 'ASC',
				'include'          => '',
				'exclude'          => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'wpdocs_folder',
				'post_mime_type'   => '',
				'post_parent'      => $post_parent,
				'author'	   => '',
				'author_name'	   => '',
				'post_status'      => 'hidden',
				'suppress_filters' => true
			);
	
			//pree($args);
			$posts_array = get_posts($args);
			if (!empty($posts_array)) {
				foreach ($posts_array as $posts) {
					$ret[] = array('id' => $posts->ID, 'title' => $posts->post_title);
				}
			}
		}
		//pree($ret);
		return $ret;
	}
	add_action('wp_ajax_wpdocs_create_folder', 'wpdocs_create_folder');
	
	function wpdocs_create_folder()
	{
	
		$post_parent = sanitize_wpdocs_data($_POST['parent_dir']);
	
		$my_post = array(
			'post_title'    => 'New Folder',
			'post_content'  => '',
			'post_status'   => 'hidden',
			'post_author'   => 1,
			'post_type'   => 'wpdocs_folder',
			'post_parent'      => (($post_parent > 0 && wpdocs_folder_exists($post_parent)) ? $post_parent : 0),
			'post_category' => array()
		);
	
		$dir_id = wp_insert_post($my_post);
		
		echo '<li class="ab-dir ab-new" data-id="'.$dir_id.'"><a class="folder fa fa-folder"></a><a class="dtitle" title="'.__('Click here to rename', 'wp-docs').'">New Folder</a><span class="wpd_action_span"><a class="wpd-edit" title="'.__('Click here to edit', 'wp-docs').'"></a><a class="wpd-move" title="'.__('Click here to move', 'wp-docs').'"></a><a class="wpd-trash" title="'.__('Click here to delete', 'wp-docs').'"></a></span></li>';
	
	
		exit;
	}
	
	
	if(!function_exists('wpd_get_icon_file_types')){
	
		function wpd_get_icon_file_types(){
	
			global $icon_sub_path, $wpdocs_dir;
	
			$ext_img_dir = $wpdocs_dir.$icon_sub_path;
	
			$file_types = file_exists($ext_img_dir) && is_dir($ext_img_dir) ? scandir($ext_img_dir) : array();
	
			$file_types = array_map(function ($file) use ($ext_img_dir){
	
				$ignore_array = array('.', '..');
				if(!in_array($file, $ignore_array) && !is_dir($ext_img_dir.$file)){
					return current(explode('.', $file));
				}
	
			}, $file_types);
	
			$file_types = array_filter($file_types);
	
			return $file_types;
	
		}
	}
	
	
	if(!function_exists('wpd_get_item_type_icon_url')){
	
		function wpd_get_item_type_icon_url($item){
			
			//pree($item);
	
			global $wpdocs_url, $icon_sub_path, $wpdocs_options, $wpdocs_pro;
	
			$ext_img_url = $wpdocs_url.$icon_sub_path;
			$file_types = wpd_get_icon_file_types();
			
			$file_url = wp_get_attachment_url($item);
			//pree($file_url);exit;
			
			
			$filename = basename($file_url);
			
			
			
			$ext = explode('.', $filename);
			$ext = end($ext);
			$icon = in_array($ext, $file_types) ? $ext.'.png' : 'unknown.png';
			$icon_url =  $ext_img_url.$icon;
			
			switch($ext){
				case 'svg':
				case 'gif':
				case 'bmp':
				case 'jpg':
				case 'jpeg':
				case 'png':
					$thumb_image = array_key_exists('thumb_image', $wpdocs_options);
					
					if($thumb_image){
						$icon_urls = wp_get_attachment_image_src($item, 'thumbnail', false);
						if(!empty($icon_urls)){
							$icon_url = current($icon_urls);
						}
					}
					//pree($icon_urls);
				break;
			}
				
				
			
	
			return array(
	
					'file_url' => $file_url,
					'ext' => $ext,
					'filename' => $filename,
					'title' => $filename,
					'icon_url' => $icon_url
			);
	
	
		}
	}
	
	add_action('wp_ajax_wpdocs_add_files', 'wpdocs_add_files');
	
	function wpdocs_add_files()
	{
	
		$dir_id = sanitize_wpdocs_data($_POST['dir_id']);
		$files = sanitize_wpdocs_data($_POST['files']);
		$files = is_array($files) ? $files : array($files);
	
	//    delete_post_meta($dir_id, 'wpdocs_items');
	
		wpdocs_update_files_meta($dir_id, $files);
		
		
		
		$ret = '';
	
		if(!empty($files)){
			$files_list = wpdocs_list_added_items($dir_id);
			
			$ret = $files_list;
		}
			
		echo $ret;
		exit;
	}
	function wpdocs_list_added_items($dir)
	{
	
		//pree(wpdocs_folder_exists($dir));
	
	
	
	
	
		$wpdocs_items = wpdocs_added_items($dir); //pree($wpdocs_items);
		$files_list = array();
	
	
		if (!empty($wpdocs_items)) {
			//pree($wpdocs_items);
	
			foreach ($wpdocs_items as $item) {
				$class = '';
	
				$item_data = wpd_get_item_type_icon_url($item);
				extract($item_data);
	
	
				/*switch ($ext) {
					case 'png':
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'bmp':
	
						$class .= 'fa-image';
	
						break;
	
					default:
						$class .= 'fa-file';
						break;
				}*/
				$class = '';
				$files_list[$title] = '<li data-id="' . $item . '" data-dir="'.$dir. '" title="'.$filename.'">
									<a href="' . $file_url . '" target="_blank" class="file  ' . $class . '"><img src="'.$icon_url.'" style=""> </a>
									<a class="ftitle" title="' . $title . '">' . $title . '</a>
									<span class="wpd_action_span">
									<a href="upload.php?item='.$item.'" target="_blank" class="wpd-edit" title="'.__('Click here to edit', 'wp-docs').'"></a>
									<a class="wpd-move" title="'.__('Click here to move', 'wp-docs').'"></a>
									<a href="upload.php?search='.$filename.'" target="_blank" class="wpd-trash" title="'.__('Click here to delete', 'wp-docs').'"></a>
									</span>
								</li>';
			}
		}
	
		ksort($files_list);
	
		return implode('', $files_list);
	}
	
	
	if(!function_exists('wpdocs_get_breadcrumb_array')){
		function wpdocs_get_breadcrumb_array($dir, $breadcrumb=true){
	
			$breadcrumb_array = array();
	
			if ($breadcrumb && $dir) {
				$dir_id = $dir;
	
				$dir_parent = wp_get_post_parent_id($dir_id);
				array_push($breadcrumb_array, $dir_id);
				array_push($breadcrumb_array, $dir_parent);
	
	
				if ($dir_parent != 0) {
					do {
	
						$dir_parent = wp_get_post_parent_id($dir_parent);
						array_push($breadcrumb_array, $dir_parent);
					} while ($dir_parent > 0);
				}
			}
	
	
			return $breadcrumb_array;
	
		}
	}
	
	add_shortcode('wpdocs', 'wpdocs_front_list');

	function wpdocs_front_list($atts=array())
	{
		ob_start();
		global $wpdocs_url, $wpdocs_options, $wpdocs_pro;
		
		$wpdocs_view = isset($_SESSION['wpdocs_view'])?$_SESSION['wpdocs_view']:'list_view';
		
		$is_bootstrap = array_key_exists('bootstrap', $wpdocs_options);
		$is_file = array_key_exists('file_upload', $wpdocs_options);
	
		$details_date = array_key_exists('details_date', $wpdocs_options);
		$details_type = array_key_exists('details_type', $wpdocs_options);
		$details_size = array_key_exists('details_size', $wpdocs_options);		 		
	
		if(isset($_POST['wpd_dir_id_ajax'])){
	
			$dir = ((isset($_POST['wpd_dir_id_ajax']) && wpdocs_folder_exists($_POST['wpd_dir_id_ajax'])) ? $_POST['wpd_dir_id_ajax'] : 0);
			if($_POST['wpd_dir_id_ajax'] == 0 && $_POST['wpd_home_id'] != 0){
				$dir = $_POST['wpd_home_id'];
			}
			$get_permalink = isset($_POST['wpd_get_permalink']) ? $_POST['wpd_get_permalink'] : '' ;
	
		}else{
	
			$dir = ((isset($_GET['dir']) && wpdocs_folder_exists($_GET['dir'])) ? $_GET['dir'] : 0);
			$get_permalink = get_permalink();
		}
		
	
	
	
		$dir = ($dir?$dir:((isset($atts['dir']) && $atts['dir']>0 && wpdocs_folder_exists($atts['dir']))?$atts['dir']:$dir));
		$no_breadcrumb = (isset($atts['breadcrumb']) && $atts['breadcrumb']=='false');
	
	
	
		if(isset($_POST['wpd_home_id'])){
	
			$home_id = $_POST['wpd_home_id'];
	
		}elseif(isset($atts['dir'])){
			$home_id = $atts['dir'];
		}else{
			$home_id = 0;
		}
		
		$wpdoc_valid = true;
		//pree($home_id);
		$wpdocs_security = get_post_meta($dir, 'wpdocs_security', true);
		//pree($wpdocs_security);
		$roles = array();
		if($wpdocs_security!=''){
			if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$roles = ( array ) $user->roles;			
				//pree($roles);
				$wpdoc_valid = in_array($wpdocs_security, $roles);
			}else{
				$wpdoc_valid = false;
			}
			
		}
		
		$breadcrumb_array = wpdocs_get_breadcrumb_array($dir, !$no_breadcrumb);
		//pree($breadcrumb_array);
		//pree($wpdoc_valid);
		$warning_msg = '';
		if(!$wpdoc_valid){
			$dir = time()*time();
			$warning_msg = '<div class="alert alert-warning fade in alert-dismissible show w-50 mx-auto">
					 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true" style="font-size:20px">×</span>
					  </button>    <strong>'.__('Sorry', 'wp-docs').'!</strong> '.__('You are not allowed to access this content.', 'wp-docs').'
					</div>';
		}
		
	
		//pree($dir.' ~ '.$no_breadcrumb);
	   
	
	
		if(is_array($breadcrumb_array) && !empty($breadcrumb_array)){
	
	
			$array_search = array_search($home_id , $breadcrumb_array);
	
			if($array_search === 0){
	
				unset($breadcrumb_array);
	
			}else{
	
				$breadcrumb_array[$array_search] = 0;
	
			}
	
			if(!empty($breadcrumb_array)){
	
				foreach ($breadcrumb_array as $index => $bread){
	
					if($index > $array_search){
						unset($breadcrumb_array[$index]);
					}
				}
			}
		}
	
		$wpdocs_list = wpdocs_list($dir);
		// $files_list = wpdocs_list_added_items($dir);
		$files_list = wpdocs_added_items($dir);
		
	
	
	
	
	
		?>
		
	
	
			<div class="container-fluid wpdoc_container" data-dir="<?php echo $dir ?>">
	
				<input type="hidden" class="wpd_home_id" value="<?php echo $home_id ?>" />
				  
	
				<div class="card mt-3">
					<?php if (!empty($breadcrumb_array)) { ?>
					<!-- breadcrumb Area -->
					<nav aria-label="breadcrumb" class="wpdocs-nav position-relative">
						<ol class="breadcrumb bg-light" style="border-bottom:1px solid #dee2e6;border-radius: 0;">
	
							<li class="breadcrumb-item bread_home_url"><a class="wpd_bread_item" href="<?php echo $get_permalink ?>" data-id="0"><?php _e('Home', 'wp-docs'); ?></a></li>
							<?php
								
									foreach (array_reverse($breadcrumb_array) as $bread_key => $bread_value) {
										$active = '';
										$page = '';
										$permalink = stripos($get_permalink, '?');
										$permalink_c = ($permalink!='' && is_numeric($permalink) && $permalink>=0);
										$link = '<a class="wpd_bread_item" href="' . $get_permalink . ($permalink_c?'&':'?').'dir=' . $bread_value . '" data-id="'.$bread_value.'" >' . get_the_title($bread_value) . '</a>';
										if ($bread_value == 0) {
											continue;
										}
										if ($bread_value == $dir) {
											$active = 'active';
											$page = 'page';
											$link = get_the_title($bread_value);
										}
	
	
										?>
									<li class="breadcrumb-item <?php echo $active ?>" aria-current="<?php echo $page ?>"><?php echo $link ?></li>
	
							<?php
									}
								
								?>
	
						</ol>
						<?php if($wpdocs_pro && $dir != 0 && current_user_can('upload_files') && $is_file):?>
	
							<i class="fa fa-plus-circle fa-1x wpdocs-front-add-media position-absolute" title="<?php _e('Click here to add files', 'wp-docs'); ?>"></i>
	
						<?php endif; ?>
					</nav>
					<?php } ?>
					
					<?php echo $warning_msg?'<div class="card-body">'.$warning_msg.'</div>':''; ?>
	
					<div class="card-body <?php echo $warning_msg?'d-none':''; ?>">
	
						<!-- Large Icon View Area -->
						<div class="row folder_view large_icon_view <?php echo $wpdocs_view=='large_icon_view'?'':'d-none'; ?>">
							<?php
								
								$no_dir_found = false;
								$no_file_found = false;
								if (!empty($wpdocs_list)) {
									foreach ($wpdocs_list as $list) {
										?>
	
									<div class="col-4 col-md-2 file_wrapper is_dir" style="cursor: pointer;" data-id="<?php echo $list['id']; ?>">
										<figure class="figure file_view p-1">									
											<span class="fa fa-folder text-warning" style="font-size:90px"></span>
											<figcaption class="figure-caption text-center"><?php echo $list['title']; ?></figcaption>
										</figure>
									</div>
								<?php
										}
									} else {
										$no_dir_found = true;
									}
	
	
									if (!empty($files_list)) {
										$list = array();
										foreach ($files_list as $file) {
	
											$file_data = wpd_get_item_type_icon_url($file);
											extract($file_data);
											
											$list[$title] = '
	
											
									<div title="'.$filename.'" class="col-4 col-md-2 is_file text-center" style="cursor: pointer;" data-id="'.$file.'">
										<figure class="figure file_view p-1">
											<a href="'.$file_url.'" target="_blank" class="file" ><img class="my-3" src="'.$icon_url.'" /></a>
											<figcaption class="figure-caption text-center">'.$title.'</figcaption>
										</figure>
									</div>';
							
										}
										//pree(array_keys($list));
	
										ksort($list);
										//pree(array_keys($list));
										echo implode('', $list);
									} else {
										$no_file_found = true;
									}
	
									if ($no_dir_found && $no_file_found) {
	
										?>
								<div class="alert alert-info text-center mx-auto">
									<strong><?php _e('Info!', 'wp-docs'); ?></strong> <?php _e('Empty Directory.', 'wp-docs'); ?>
								</div>
							<?php } ?>
	
	
						</div>
	
						<!-- List View Area -->
						<div class="row folder_view list_view <?php echo $wpdocs_view=='list_view'?'':'d-none'; ?>">
							<?php
								$no_dir_found = false;
								$no_file_found = false;
								if (!empty($wpdocs_list)) {
									foreach ($wpdocs_list as $list) {
										?>
	
									<div class="col-12 file_wrapper is_dir" style="cursor: pointer;" data-id="<?php echo $list['id']; ?>">
										<figure class="figure file_view p-1">																				
											<span class="fa fa-folder text-warning" style="font-size:25px"></span>
											<small class="text-center"><?php echo $list['title']; ?></small>
										</figure>
									</div>
								<?php
										}
									} else {
										$no_dir_found = true;
									}
	
	
									if (!empty($files_list)) {
										$list = array();
										foreach ($files_list as $file) {
	
											$file_data = wpd_get_item_type_icon_url($file);
											extract($file_data);
											
											$list[$title] = '
									<div title="'.$filename.'" class="col-12 file_wrapper is_file" style="cursor: pointer;" data-id="'.$file.'">
										<figure class="figure file_view p-1">
											<a href="'.$file_url.'" target="_blank" class="file" ><img class="mb-2" src="'.$icon_url.'" style="width: 25px; height: 25px"></a>
											<small class="text-center">'.$title.'</small>
										</figure>
									</div>';
	
										}
										//pree($list);
										ksort($list);
										echo implode('', $list);
									} else {
										$no_file_found = true;
									}
	
									if ($no_dir_found && $no_file_found) {
	
										?>
								<div class="alert alert-info text-center mx-auto">
									<strong><?php _e('Info!', 'wp-docs'); ?></strong> <?php _e('Empty Directory.', 'wp-docs'); ?>
								</div>
							<?php } ?>
	
	
						</div>
	<?php
	
	?>
						<!-- Detail View Area -->
						<div class="row folder_view detail_view mt-0 <?php echo $wpdocs_view=='detail_view'?'':'d-none'; ?>">
							<div class="table-responsive" style="zoom:70%">
								<table class="table">
									<thead class="thead">
										<tr>
											<td><?php _e('Name', 'wp-docs'); ?></td>
	<?php if($details_date): ?>				<td><?php _e('Modified Date', 'wp-docs'); ?></td><?php endif; ?>
	<?php if($details_type): ?>				<td><?php _e('Type', 'wp-docs'); ?></td><?php endif; ?>
	<?php if($details_size): ?>				<td><?php _e('Size', 'wp-docs'); ?></td><?php endif; ?>
										</tr>
									</thead>
									<?php
										$no_dir_found = false;
										$no_file_found = false;
										if (!empty($wpdocs_list)) {
											foreach ($wpdocs_list as $list) {
												?>
											<tr class="file_wrapper file_view is_dir" style="cursor: pointer;" data-id="<?php echo $list['id']; ?>">
												<td>
													<figure class="figure ">
														<span class="fa fa-folder text-warning" style="font-size:25px"></span>
														<small class="text-center mb-1"><?php echo $list['title']; ?></small>
													</figure>
												</td>
												
	<?php if($details_date): ?>					<td><small><?php echo get_the_modified_date(get_option( 'date_format' ), $list['id']) . ' ' . get_the_modified_time(get_option( 'time_format' ), $list['id']) ?></small></td><?php endif; ?>
	<?php if($details_type): ?>					<td><small><?php echo get_post_type($list['id'])!='wpdocs_folder'?get_post_type($list['id']):'Directory' ?></small></td><?php endif; ?>
	<?php if($details_size): ?>					<td><small></small></td><?php endif; ?>
											</tr>
										<?php
												}
											} else {
												$no_dir_found = true;
											}
	
	
											if (!empty($files_list)) {
												$list = array();
												foreach ($files_list as $file) {
	
													$file_data = wpd_get_item_type_icon_url($file);
													extract($file_data);
													
													$files_list_row = '
													<tr title="'.$filename.'" data-url="'.$file_url.'" class="file_view file_link" style="cursor: pointer;" data-id="'.$file.'">
														<td>
															<figure class="figure file_view p-1">
																<span class="file"><img class="mb-2" src="'.$icon_url.'" style="width: 25px; height: 25px"></span>
																<small class="text-center">'.$title.'</small>
															</figure>
														</td>
													';
													
													if($details_date): $files_list_row .= '<td><small>'.get_the_modified_date(get_option( 'date_format' ), $file) . ' ' . get_the_modified_time(get_option( 'time_format' ), $file).'</small></td>'; endif; 
													if($details_type): $files_list_row .= '<td><small>'.get_post_mime_type($file).'</small></td>'; endif;
													if($details_size): $files_list_row .= '<td><small>'.(round(filesize(get_attached_file($file)) / 1024)) . ' KB'.'</small></td>'; endif;
													
													$files_list_row .= '</tr>';
													$list[$title] = $files_list_row;	
	
												}
											
												ksort($list);
												echo implode('', $list);
												
											} else {
												$no_file_found = true;
											}
	
											if ($no_dir_found && $no_file_found) {
	
												?>
										<tr>
											<td class="alert alert-info text-center mx-auto" colspan="4">
												<strong><?php _e('Info!', 'wp-docs'); ?></strong> <?php _e('Empty Directory.', 'wp-docs'); ?>
											</td>
										</tr>
									<?php } ?>
								</table>
							</div>
						</div>
	
					</div>
					<div class="card-footer text-right wpdocs-views position-relative">
	
	
	
						<a data-source="large_icon_view" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Thumbnails View', 'wp-docs'); ?>" class="folder_view_btn fa fa-image fa-lg text-danger mr-2"></a>
						<a data-source="list_view" data-toggle="tooltip" data-placement="bottom" title="<?php _e('List View', 'wp-docs'); ?>" class="folder_view_btn fa fa-bars fa-lg text-danger mr-2"></a>
						<a data-source="detail_view" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Details Views', 'wp-docs'); ?>" class="folder_view_btn fa fa-list fa-lg text-danger mr-2"></a>
					</div>
				</div>
	<?php if($is_bootstrap): ?>
				<div class="wpdocs_loader wpd_modal d-none">
					<div class="modal_content">
						<img src="<?php echo $wpdocs_url.'img/loader.gif' ?>" width="50px" height="50px">
					</div>
				</div>
	<?php endif; ?>            
	
			</div>
	
	
	
		<?php
	
	
	
			$out1 = ob_get_contents();
	
			ob_end_clean();
	
			return $out1;
	}


	function wpdocs_parent_folder($id)
	{
		//pree($id);
		$parent_id = 0;
		if (wpdocs_folder_exists($id)) {
			$post_data = get_post($id);
			//pree($post_data);
			$parent_id = $post_data->post_parent;
		}
		return ($parent_id);
	}

	function wpdocs_added_items($dir_id)
	{
		$wpdocs_items = array();
		if (is_numeric($dir_id) && $dir_id > 0 && wpdocs_folder_exists($dir_id)) {
			$wpdocs_items = get_post_meta($dir_id, 'wpdocs_items', true);
			//pree($wpdocs_items);
			$wpdocs_items = is_array(maybe_unserialize($wpdocs_items)) ? maybe_unserialize($wpdocs_items) : array();
			//pree($wpdocs_items);
			//asort($wpdocs_items);
		}
		return $wpdocs_items;
	}

	function wpdocs_folder_exists($id)
	{
		//pree($id);
		$posts_array = array();
		if (is_numeric($id) && $id > 0) {
			$args = array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'title',
				'order'            => 'ASC',
				'include'          => array($id),
				'exclude'          => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'wpdocs_folder',
				'post_mime_type'   => '',
				'post_parent'      => '',//$post_parent,
				'author'	   => '',
				'author_name'	   => '',
				'post_status'      => 'hidden',
				'suppress_filters' => true
			);
			$posts_array = get_posts($args);
		}
		return (count($posts_array) > 0);
	}

	add_action('wp_ajax_wpdocs_update_folder', 'wpdocs_update_folder');
	

	
	function wpdocs_update_folder()
	{

		$dir_id = sanitize_wpdocs_data($_POST['dir_id']);

		if ($dir_id > 0 && wpdocs_folder_exists($dir_id)) {

			$my_post = array(
				'post_title'    => sanitize_wpdocs_data($_POST['new_name']),
				'ID'  => $dir_id,
			);

			wp_update_post($my_post);
		}


		exit;
	}
	
	add_action('wp_ajax_wpdocs_delete_folder', 'wpdocs_delete_folder');

	function wpdocs_delete_folder()
	{

		$dir_id = sanitize_wpdocs_data($_POST['dir_id']);
		wpdocs_recursive_delete_folder($dir_id);
		
		exit;
	}	


	
	add_action('wp_ajax_wpdocs_delete_files', 'wpdocs_delete_files');
	
	

	function wpdocs_recursive_delete_folder($dir_id){
		if ($dir_id > 0 && wpdocs_folder_exists($dir_id)) {
			$wpdocs_list = wpdocs_list($dir_id);
			if(!empty($wpdocs_list)){
				foreach($wpdocs_list as $wpdocs_item){
					//pree($wpdocs_item);
					if(is_numeric($wpdocs_item['id'])){
						wpdocs_recursive_delete_folder($wpdocs_item['id']);
					}
				}
			}
			
			
			wp_delete_post($dir_id, true);
		}
		
	}

	if(!function_exists('wpdocs_update_files_meta')){

	    function wpdocs_update_files_meta($dir_id, $files=array()){

            if ($dir_id > 0 && wpdocs_folder_exists($dir_id) && count($files) > 0) {


                $wpdocs_items = wpdocs_added_items($dir_id);

                $wpdocs_items = array_merge($wpdocs_items, $files);

                $wpdocs_items = array_unique($wpdocs_items);

                //pree($wpdocs_items);

               return update_post_meta($dir_id, 'wpdocs_items', $wpdocs_items);
            }
        }
    }


	
	function wpdocs_delete_files()
	{

		$dir_id = sanitize_wpdocs_data($_POST['dir_id']);
		$files = sanitize_wpdocs_data($_POST['files']);
		$files = is_array($files) ? $files : array($files);
		//pree($dir_id);pree($files);exit;
		if ($dir_id > 0 && wpdocs_folder_exists($dir_id) && count($files) > 0) {


			$wpdocs_items = wpdocs_added_items($dir_id);
			//pree($wpdocs_items);
			$wpdocs_items = array_diff($wpdocs_items, $files);
			//pree($wpdocs_items);
			$wpdocs_items = array_unique($wpdocs_items);

			//pree($wpdocs_items);

			update_post_meta($dir_id, 'wpdocs_items', $wpdocs_items);
		}


		exit;
	}	
	
	function wpd_admin_footer(){
		
?>
<script type="text/javascript" language="javascript">

</script>
	
<?php		
		
	}
	add_action('admin_footer', 'wpd_admin_footer');

add_action('wp_ajax_wpdocs_update_option', 'wpdocs_update_option');

if(!function_exists('wpdocs_update_option')){
    function wpdocs_update_option(){



        if(isset($_POST['wpdocs_update_option_nonce'])){

            $nonce = $_POST['wpdocs_update_option_nonce'];

            $return = array(

                'option_update' => false,
                'dir_move' => false,
            );

            if ( ! wp_verify_nonce( $nonce, 'wpdocs_update_options_nonce' ) )
                die (__("Nonce didn't verified.", 'wp-docs'));

            if(isset($_POST['wpdocs_options'])){

                $wpdocs_options = isset($_POST['wpdocs_options']) ? $_POST['wpdocs_options'] : array();
                $update = update_option('wpdocs_options', sanitize_wpdocs_data($wpdocs_options));
            }



            if(isset($_POST['wpdocs_move_selected_dir'])){

                $wpdocs_move_selected_dir = sanitize_wpdocs_data($_POST['wpdocs_move_selected_dir']);

                $is_file = array_key_exists('is_file', $wpdocs_move_selected_dir) ? $wpdocs_move_selected_dir['is_file']: false;
                $is_file = $is_file == 'false' ? false: true;


                if(!$is_file && array_key_exists('dir_selected', $wpdocs_move_selected_dir) &&
                    array_key_exists('dir_id', $wpdocs_move_selected_dir)){

                    $update = wp_update_post(
                        array(
                            'ID' => $wpdocs_move_selected_dir['dir_selected'],
                            'post_parent' => $wpdocs_move_selected_dir['dir_id']
                        )
                    );
                    if($update == $wpdocs_move_selected_dir['dir_selected']){
                        $return['dir_move'] = true;
                    }
                }


                if($is_file){

                    $file_id = $wpdocs_move_selected_dir['files'];
                    $file_dir = $wpdocs_move_selected_dir['file_dir'];
                    $new_dir = $wpdocs_move_selected_dir['dir_id'];
                    $files =  wpdocs_added_items($file_dir);
                    $file_id = is_array($file_id) ? $file_id : array($file_id);
                    $files = array_diff($files, $file_id);
                    update_post_meta($file_dir, 'wpdocs_items', $files);
                    $update = wpdocs_update_files_meta($new_dir, $file_id);

                    if($update === true){
                        $return['dir_move'] = true;
                    }


                }
            }

            echo  json_encode($return);

        }

        wp_die();

    }
}



if(!function_exists('wpdocs_dir_list_complete')){

    function wpdocs_dir_list_complete($dir = 0){

          $wpdocs_list = wpdocs_list($dir);

          if(!empty($wpdocs_list)){

              $wp_dir_child = array();

              foreach ($wpdocs_list as $index => $wp_dir){

                  if(!array_key_exists('id', $wp_dir)) continue;
                  $wpdocs_list_child = wpdocs_list($wp_dir['id']);

                  if(!empty($wpdocs_list_child)){

                      $wp_dir['child_dir'] = wpdocs_dir_list_complete($wp_dir['id']);

                  }

                  $wp_dir_child[] = $wp_dir;


              }

              return $wp_dir_child;

          }else{

              return array();
          }
    }
}

if(!function_exists('wpdocs_dir_list_option')){

    function wpdocs_dir_list_option($dir = 0, $str = ' __ ', $level = 0){


        $wpdocs_list = wpdocs_list($dir);

        $option = '';

        if(!empty($wpdocs_list)){


            foreach ($wpdocs_list as $index => $wp_dir){

                if(!array_key_exists('id', $wp_dir)) continue;
                $wpdocs_list_child = wpdocs_list($wp_dir['id']);

                $option .= '<option value="'.$wp_dir['id'].'" data-parent="'.$dir.'">'.str_repeat(str_replace(' ', '&nbsp;', $str), $level).$wp_dir['title'].'</option>';


                if(!empty($wpdocs_list_child)){


                    $option .= wpdocs_dir_list_option($wp_dir['id'], $str, $level+1);

                }

            }


        }

            return $option;
    }
}

add_action('wpdocs_before_docs_list', 'wpdocs_add_breadcrumb');

if(!function_exists('wpdocs_add_breadcrumb')){

    function wpdocs_add_breadcrumb($dir_id, $breadcrumb=true){

        $breadcrumb_array = wpdocs_get_breadcrumb_array($dir_id, $breadcrumb);
        $get_permalink = admin_url('options-general.php?page=wpdocs');

		if (!empty($breadcrumb_array)) {
        ?>

        <nav aria-label="breadcrumb" class="wpdocs-nav">
            <ol class="breadcrumb bg-light" style="border-bottom:1px solid #dee2e6;border-radius: 0;">

                <li class="breadcrumb-item bread_home_url"><a class="wpd_bread_item" href="<?php echo $get_permalink ?>" data-id="0">Home</a></li>
                <?php
                
                    foreach (array_reverse($breadcrumb_array) as $bread_key => $bread_value) {
                        $active = '';
                        $page = '';

                        $link = '<a class="wpd_bread_item" href="' . $get_permalink .'&dir=' . $bread_value . '" data-id="'.$bread_value.'" >' . get_the_title($bread_value) . '</a>';
                        if ($bread_value == 0) {
                            continue;
                        }
                        if ($bread_value == $dir_id) {
                            $active = 'active';
                            $page = 'page';
                            $link = get_the_title($bread_value);
                        }


                        ?>
                        <li class="breadcrumb-item <?php echo $active ?>" aria-current="<?php echo $page ?>"><?php echo $link ?></li>

                        <?php
                    }
                
                ?>

            </ol>

        </nav>

        <?php
		
		}
    }
}

	add_action('wp_ajax_wpdocs_update_view', 'wpdocs_update_view');
	add_action('wp_ajax_nopriv_wpdocs_update_view', 'wpdocs_update_view');
	
	if(!function_exists('wpdocs_update_view')){
		function wpdocs_update_view(){
	
	
			if(isset($_POST['update_view'])){
				$_SESSION['wpdocs_view'] = $_POST['update_view'];
			}
			exit;
		}
	}
		
	function wpdocs_init_session() {
		if(!session_id()) {
			session_start();
		}
	}
	
	add_action('init', 'wpdocs_init_session', 1);

    if(!function_exists('wpdocs_create_dir')){
        function wpdocs_create_dir($dir_id, $parent_path){

            if(!file_exists($parent_path)){
                mkdir($parent_path);
            }
            $current_dir_name = 'wpdocs';
            if($dir_id != 0){

                $current_dir = get_post($dir_id);
                $current_dir_name = $current_dir->post_title;
            }

            $current_dir_temp = $parent_path.'/'.$current_dir_name;

            if(!file_exists($current_dir_temp))
                mkdir($current_dir_temp);

            return $current_dir_temp;
        }
    }

    if(!function_exists('wpdocs_copy_files')){
        function wpdocs_copy_files($wpdocs_items, $current_dir_temp){

            $upload_dir = wp_upload_dir()['basedir'];
            if(!empty($wpdocs_items)){
                foreach ($wpdocs_items as $item_id){

                    $attached_file = get_post_meta($item_id, '_wp_attached_file', true);
                    $file_name = basename($attached_file);
                    $file_copy_path = $current_dir_temp.'/'.$file_name;
                    $file_path = $upload_dir.'/'.$attached_file;

                    copy($file_path, $file_copy_path);
                }
            }

        }
    }

    if(!function_exists('wpdocs_create')){

        function wpdocs_create($dir_id, $wpdocs_dir){



            $wpdocs_list = wpdocs_list($dir_id);
            $wpdocs_items = wpdocs_added_items($dir_id);

            $current_dir_temp = wpdocs_create_dir($dir_id, $wpdocs_dir);
            wpdocs_copy_files($wpdocs_items, $current_dir_temp);

            if(!empty($wpdocs_list)){
                foreach ($wpdocs_list as $single_dir){
                    wpdocs_create($single_dir['id'], $current_dir_temp);
                }
            }

        }

    }

    if(!function_exists('wpdocs_generate_zip')){

        function wpdocs_generate_zip($source, $destination)
        {

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);


            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST
            );



            if (!empty($files)) {
                foreach ($files as $name => $file) {

                    // Skip directories (they would be added automatically)
                    if (!$file->isDir()) {
                        // Get real and relative path for current file
                        $file_path = $file->getRealPath();
                        $relative_path = substr($file_path, strlen($source) + 1);

                        // Add current file to archive
                        $zip->addFile($file_path, $relative_path);


                    }
                }
            }



            // Zip archive will be created only after closing object
            $zip->close();

            $ret = str_replace('\\', '/', $destination);
            return array(
                'url' => str_replace(get_home_path(), get_home_url().'/', $ret),
                'path' => $destination,
            );

        }

    }

    if(!function_exists('wpdocs_download_zip')){
        function wpdocs_download_zip($dir_id){

            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'];
            $wpdocs_dir = $upload_dir.'/wpdocs';
            $current_dir_temp = wpdocs_create_dir($dir_id, $wpdocs_dir);
            wpdocs_create($dir_id, $wpdocs_dir);

            $dest = $wpdocs_dir.'/'.basename($current_dir_temp).'.zip';
            return  wpdocs_generate_zip($current_dir_temp, $dest);

        }
    }
	
    add_action('init', 'wpdocs_dir_download');
    if(!function_exists('wpdocs_dir_download')){
        function wpdocs_dir_download(){
			
			
			
            if(isset($_GET['wpdocs_dir']) && is_numeric($_GET['wpdocs_dir']) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], "wpdocs-{$_GET['wpdocs_dir']}" )){
				
				

                $zip =  wpdocs_download_zip($_GET['wpdocs_dir']);
                $rm_dir = str_replace('.zip', '',$zip['path'] );
				
				//echo $rm_dir;

                if (is_dir($rm_dir)) {
                    $dir = new RecursiveDirectoryIterator($rm_dir, RecursiveDirectoryIterator::SKIP_DOTS);
                    foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST ) as $filename => $file) {
                        if (is_file($filename))
                            unlink($filename);
                        else
                            rmdir($filename);
                    }
                    rmdir($rm_dir); // Now remove myfolder
                }
				
				
				//echo $zip['path'];exit;


                header("Content-type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zip['path'])."");
                header("Pragma: no-cache");
                header("Expires: 0");
                header("Content-length: " . filesize($zip['path']));
                readfile($zip['path']);

                unlink($zip['path']);
                wp_die();

            }

        }

    }