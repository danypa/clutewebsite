<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php include("inc/plugin_header.php");?>

<?php 
if(empty($_GET['tab'])){
	include("inc/rearrange_all.php");
}
if(!empty($_GET['tab']) && $_GET['tab']=='groupby-categories'){
	include("inc/rearrange_by_categories.php");
}
if(!empty($_GET['tab']) && $_GET['tab']=='groupby-tags'){
	include("inc/rearrange_by_tags.php");
}
?>

<?php include("inc/plugin_footer.php");?>