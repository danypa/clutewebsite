<div class="wrap wpdocs-wrapper">

<?php
    global $wpdocs_url;
	wpdocs_downward_compatibility();
	$dir = ((isset($_GET['dir']) && $_GET['dir']>0)?$_GET['dir']:0);
	$files_list = wpdocs_list_added_items($dir);

    $wpdocs_options = get_option('wpdocs_options', array());

//    pree($wpdocs_options);exit;
    $is_ajax = array_key_exists('ajax', $wpdocs_options);
    $is_ajax_url = array_key_exists('ajax_url', $wpdocs_options);
	$is_bootstrap = array_key_exists('bootstrap', $wpdocs_options);
	$is_file = array_key_exists('file_upload', $wpdocs_options);
	$thumb_image = array_key_exists('thumb_image', $wpdocs_options);
	
	$details_date = array_key_exists('details_date', $wpdocs_options);
	$details_type = array_key_exists('details_type', $wpdocs_options);
	$details_size = array_key_exists('details_size', $wpdocs_options);

	$is_filename = array_key_exists('filename', $wpdocs_options);

	//pree($wpdocs_options);




    $dir_id = $dir;


	
?>

<div class="wpdocs_in_action">

<?php
	$wpdocs_security = ucwords(get_post_meta($dir_id, 'wpdocs_security', true));
	
	$security_level = __('Security Level:', 'wp-docs').' '.($wpdocs_security?$wpdocs_security:__('None', 'wp-docs'));

	$role_arr = function_exists('wpd_get_roles_select')?wpd_get_roles_select($dir_id):'<a title="'.__('Security Level is a Premium Feature', 'wp-docs').'" class="security_level" href="'.$wpdocs_premium_link.'" target="_blank">'.$security_level.' <i class="fa fa-lock"></i></a>';

	
	$download_nonce = wp_create_nonce( 'wpdocs-'.$dir_id );
	
?>	
	

<div class="wpdocs_folders">
<div class="wpdocs_toolbar">
<ul><li><a class="back-folder fa fa-hand-o-left" title="<?php _e('Click here to go back', 'wp-docs'); ?>" data-parent="<?php echo wpdocs_parent_folder($dir); ?>" data-id="<?php echo ($dir); ?>"></a></li>
<?php if($dir>0): ?>
<li><a class="new-file" data-id="<?php echo $dir; ?>"><i class="fa fa-plus-circle"></i><?php _e('Add Files', 'wp-docs'); ?></a></li>
<?php endif; ?>
<li><a class="new-folder" data-id="<?php echo $dir; ?>"><?php _e('New folder', 'wp-docs'); ?></a></li>
<li class="wpdocs_move_folder_to" >

    <select title="<?php _e('Move selected folder to..', 'wp-docs'); ?>">
        <option value="-1"><?php _e('Select directory to move', 'wp-docs'); ?></option>
        <option value="0"><?php _e('Root', 'wp-docs'); ?></option>
        <?php echo wpdocs_dir_list_option(); ?>
    </select>
    <button><?php _e('Confirm', 'wp-docs'); ?></button>
</li>
<li>
<?php echo $role_arr; ?>
</li>
<li style="float:right">
<a style="font-size: 12px;color: red;margin: 8px 0 0 0;display: block;" href="https://www.youtube.com/embed/<?php echo $wpdocs_pro?'cV-u3Iyt8kc':'k5bZqZ5dW30'; ?>" target="_blank"><?php _e('Video Tutorial', 'wp-docs'); ?></a>
</li>

</ul>
</div>
<div class="wpdocs_list">

    <?php do_action('wpdocs_before_docs_list', $dir_id) ?>



<ul>
<?php $wpdocs_list = wpdocs_list($dir); if(!empty($wpdocs_list)){ foreach($wpdocs_list as $list){ ?>
	<li class="ab-dir" data-id="<?php echo $list['id']; ?>"><a class="folder fa fa-folder"></a><a class="dtitle" title="<?php _e('Click here to rename', 'wp-docs'); ?>"><?php echo ($list['title']?$list['title']:'&nbsp;'); ?></a><?php echo '<span class="wpd_action_span"><a class="wpd-edit" title="'.__('Click here to edit', 'wp-docs').'"></a><a class="wpd-move" title="'.__('Click here to move', 'wp-docs').'"></a><a class="wpd-trash" title="'.__('Click here to delete', 'wp-docs').'"></a></span>'; ?></li>
<?php } }?>    
<?php echo ($files_list!=''?$files_list:''); ?>
</ul>
</div>
</div>
<div class="wpdocs_log">
    <div class="row">
        <div class="col-4 text-center">
            <a class="btn btn-light btn-sm p-1" title="<?php _e('Click here to download current directory', 'wp-docs'); ?>" href="<?php echo admin_url('options-general.php?page=wpdocs&wpdocs_dir='.$dir_id.'&_wpnonce='.esc_attr($download_nonce)); ?>" data-dir_id="<?php echo $dir_id ?>">
                <i class="fa fa-download"></i>&nbsp;&nbsp;<?php _e('Download', 'wp-docs'); ?>
            </a>
        </div>
        <div class="col-4 text-center"><?php _e('Shortcodes', 'wp-docs'); ?></div>
        <div class="col-4 text-center"></div>
    </div>
<center></center><br /><br />
[wpdocs<?php echo (isset($_GET['dir']) && $_GET['dir']>0)?' dir="'.$_GET['dir'].'"':''; ?> breadcrumb="true"]

<hr />

<div class="row nopadding wpdocs-options">
<?php if(!$wpdocs_pro): ?>
<a class="btn btn-warning btn-sm mx-auto" href="<?php echo $wpdocs_premium_link; ?>" target="_blank" title="<?php echo __('Click here for Premium Version', 'wp-docs'); ?>"><?php echo __('Go Premium', 'wp-docs'); ?></a>
<?php endif; ?>


<div class="alert alert-secondary fade in alert-dismissible d-none mx-auto mt-4" style="width: 90%">
 <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('Close', 'wp-docs'); ?>">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button>    <strong><?php echo __('Success!', 'wp-docs'); ?></strong> <?php echo __('Options are updated successfully.', 'wp-docs'); ?>
</div>

<ul class="col col-md-12 mt-4">
    <li>
        <label for="wpdocs_options_bootstrap">
            <input <?php checked($is_bootstrap); ?> type="checkbox" name="wpdocs_options[bootstrap]" value="bootstrap" id="wpdocs_options_bootstrap"  />
            <?php echo __('Bootstrap Based', 'wp-docs'); ?> <small><?php echo __('(Front-end)', 'wp-docs'); ?></small>
        </label>

    </li>
    


    <li>
        <label for="wpdocs_options_thumb">
            <input <?php checked($thumb_image); ?> type="checkbox" name="wpdocs_options[thumb_image]" value="thumb_image" id="wpdocs_options_thumb"  />
            <?php echo __('Image Thumbnails', 'wp-docs'); ?> <small><?php echo __('(Optional)', 'wp-docs'); ?></small>
        </label>

    </li>    

    
    <li>
        <label for="wpdocs_options_details_view">            
            <?php echo __('Details View Columns Settings', 'wp-docs'); ?> <small><?php echo __('(Optional)', 'wp-docs'); ?></small>
        </label>
        <ul class="ml-4">
            <li>
                <label for="wpdocs_options_details_date">
                    <input <?php checked($details_date); ?> type="checkbox" name="wpdocs_options[details_view]" value="details_date" id="wpdocs_options_details_date"  />
                    <?php echo __('Date Modified', 'wp-docs'); ?> <small>(<?php echo date_i18n( get_option( 'date_format' ) ).' '.date_i18n( get_option( 'time_format' ) ); ?>)</small>
                </label>
            </li>
            <li>
                <label for="wpdocs_options_details_type">
                    <input <?php checked($details_type); ?> type="checkbox" name="wpdocs_options[details_view]" value="details_type" id="wpdocs_options_details_type"  />
                    <?php echo __('Item Type', 'wp-docs'); ?> <small></small>
                </label>
            </li>
            <li>
                <label for="wpdocs_options_details_size">
                    <input <?php checked($details_size); ?> type="checkbox" name="wpdocs_options[details_view]" value="details_size" id="wpdocs_options_details_size"  />
                    <?php echo __('Item Size', 'wp-docs'); ?> <small></small>
                </label>
            </li>                            
        </ul>
    </li>    



        
    <li class="premium-features"></li>
    
    <li>
        <label for="wpdocs_options_filename">
            <input <?php checked($is_filename); ?> type="checkbox" name="wpdocs_options[filename]" value="filename" id="wpdocs_options_filename"  />
            <?php echo __('Sort By Filename or File Title?', 'wp-docs'); ?> <small><?php echo __('(Default: Filename)', 'wp-docs'); ?></small>
        </label>

    </li>    
    <li>
    
        <label for="wpdocs_options_file">
            <input <?php checked($is_file); ?> type="checkbox" name="wpdocs_options[file_upload]" value="file_upload" id="wpdocs_options_file"  />
            <?php echo __('File Upload Front-end', 'wp-docs'); ?> <small><?php echo $wpdocs_pro?__('(Optional)', 'wp-docs'):__('(Premium)', 'wp-docs'); ?></small> <i title="<?php echo __('This icon will appear on front-end for users', 'wp-docs'); ?>" class="fa fa-plus-circle" style="color:#ffc107"></i>
        </label>

    </li>    
    <li>
        <label for="wpdocs_options_ajax">
            <input <?php checked($is_ajax); ?> type="checkbox" name="wpdocs_options[ajax]" value="ajax" id="wpdocs_options_ajax"  />
            <?php echo __('Ajax Based Directory Navigation', 'wp-docs'); ?> <small><?php echo $wpdocs_pro?__('(Optional)', 'wp-docs'):__('(Premium)', 'wp-docs'); ?></small>
        </label>
        <ul class="ml-4 <?php echo $is_ajax ? '' : 'd-none'?>">
            <li>
                <label for="wpdocs_options_ajax_url">
                    <input <?php checked($is_ajax && $is_ajax_url); ?> type="checkbox" name="wpdocs_options[ajax_url]" value="ajax_url" id="wpdocs_options_ajax_url"  />
                    <?php echo __('Update URI with Directory ID', 'wp-docs'); ?> <small><?php echo $wpdocs_pro?__('(Optional)', 'wp-docs'):__('(Premium)', 'wp-docs'); ?></small>
                </label>
            </li>
        </ul>
    </li>
    
    
	<li>
        <label for="wpdocs_options_customization">            
            <?php echo __('Appearance Customization', 'wp-docs'); ?> <small><?php echo $wpdocs_pro?__('(Optional)', 'wp-docs'):__('(Premium)', 'wp-docs'); ?></small>
        </label>
        <ul class="ml-4">
            <li>
                <label for="wpdocs_options_box_bg_color">
                    <input type="color" name="wpdocs_options[box_bg_color]" value="<?php echo array_key_exists('box_bg_color', $wpdocs_options)?$wpdocs_options['box_bg_color']:''; ?>" id="box_bg_color"  />
                    <?php echo __('Directory Background Color', 'wp-docs'); ?> <small></small>
                </label>
            </li>
            <li>
                <label for="wpdocs_options_box_txt_color">
                    <input type="color" name="wpdocs_options[box_txt_color]" value="<?php echo array_key_exists('box_txt_color', $wpdocs_options)?$wpdocs_options['box_txt_color']:''; ?>" id="box_txt_color"  />
                    <?php echo __('Directory Text Color', 'wp-docs'); ?> <small></small>
                </label>
            </li> 
            
            <li>
                <label for="wpdocs_options_box_hbg_color">
                    <input type="color" name="wpdocs_options[box_hbg_color]" value="<?php echo array_key_exists('box_hbg_color', $wpdocs_options)?$wpdocs_options['box_hbg_color']:''; ?>" id="box_hbg_color"  />
                    <?php echo __('Directory Hover Background Color', 'wp-docs'); ?> <small></small>
                </label>
            </li>  
            
            <li>
                <label for="wpdocs_options_box_htxt_color">
                    <input type="color" name="wpdocs_options[box_htxt_color]" value="<?php echo array_key_exists('box_htxt_color', $wpdocs_options)?$wpdocs_options['box_htxt_color']:''; ?>" id="box_htxt_color"  />
                    <?php echo __('Directory Hover Text Color', 'wp-docs'); ?> <small></small>
                </label>
            </li>                                    
        </ul>
    </li>    
</ul>



<a class="btn btn-warning btn-sm mx-auto " href="http://demo.androidbubble.com/educational-institution" target="_blank" title="<?php echo __('Click here for demo', 'wp-docs'); ?>"><?php echo __('Click here for demo', 'wp-docs'); ?></a>


<ul class="col col-md-12 mt-4">
	<li class="promotions"></li>
    <li style="text-align:center;">
    <a href="https://wordpress.org/plugins/gulri-slider" target="_blank" title="<?php echo __('Image Slider', 'wp-docs'); ?>"><img src="<?php echo $wpdocs_url; ?>img/gslider.gif" /></a>
    </li>
</ul>
</div>

</div>

	
</div>

</div>	