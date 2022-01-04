<?php
	add_action('admin_init', 'wpdocs_version_type_update');
	
	function wpdocs_version_type_update(){

				if(isset($_POST['version_type'])){			
					if ( 
						! isset( $_POST['version_type_nonce_field'] ) 
						|| ! wp_verify_nonce( $_POST['version_type_nonce_field'], 'version_type_action' ) 
					) {
					
					   print _e('Sorry, your nonce did not verify.', 'wp-docs');
					   exit;
					
					} else {
					
					   // process form data
					
						
							
							update_option('wpdocs_versions_type', sanitize_wpdocs_data($_POST['version_type']));
							
							if($_POST['version_type']=='new')
							wp_redirect('options-general.php?page=wpdocs');
							else
							wp_redirect('admin.php?page=wpdocs-engine.php');
							
							exit;
						
					}
				}
	}
				
	function wpdocs_downward_compatibility(){
		global $wpdocs_data, $wpdocs_versions_type, $wpdocs_pro;
?>		
			<h2><?php echo $wpdocs_data['Name']; ?> <?php echo '('.$wpdocs_data[__('Version', 'wp-docs')].($wpdocs_pro?') Pro':')'); ?> - <?php _e('Settings', 'wp-docs'); ?></h2>
            <?php if(!$wpdocs_pro): ?>
            <a style="float:right; position:relative; top:-40px; display:none;" href="<?php echo $wpdocs_premium_link; ?>" target="_blank"><?php _e('Go Premium', 'wp-docs'); ?></a>
            <?php endif; ?>
            
            
            <?php 
							

				$wpdocs_versions_type = get_option('wpdocs_versions_type', 'old');
			?>
            
            <style type="text/css">
				.versions_type{
					background-color:#CCC;
					border-radius:4px;
					padding:10px 20px 20px 20px;
					display:none;
					
				}
				.versions_type label{
					font-weight:normal;
					padding:0;
					margin:0;					
				}
				.versions_type input[type="radio"]{
					padding:0;
					margin:0;
				}
			</style>
            <script type="text/javascript" language="javascript">
				jQuery(document).ready(function($){
					$('.versions_type input[type="radio"]').on('click', function(){
						$('.versions_type > form').submit();
					});
				});				
			</script>
            
            <div class="versions_type">
            	<form action="" method="post">
                <?php wp_nonce_field( 'version_type_action', 'version_type_nonce_field' ); ?>
                </form>
            </div>
            <div class="wpdocs_help">
            	<h6><?php _e('How it works?', 'wp-docs'); ?></h6>
                <p><?php echo __('A default page is created with title', 'wp-docs').' "WP Docs" '.__('in', 'wp-docs').' <a href="edit.php?post_type=page" target="_blank">'.__('pages', 'wp-docs').'</a>. '.__('You can create more pages with a shortcode', 'wp-docs').' <code>[wpdocs]</code>. '.__('Create directories, sub-directories and add documents to list them with the shortcode.', 'wp-docs').' '.__("That's it.", 'wp-docs'); ?></p>
            </div>
<?php
	}