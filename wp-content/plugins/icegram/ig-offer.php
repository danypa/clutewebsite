<style type="text/css">
.ig_offer{
    width: 60%;
    margin: 0 auto;
    text-align: center;
    padding-top: 1.2em;
}
.ig_offer img{
	/*width: 100%;*/
}
</style>
<?php 
if(  (get_option('ig_offer_bfcm_done_2019_icegram') !== 1) && ($ig_current_date >= strtotime("2019-11-22")) && ($ig_current_date <= strtotime("2019-11-30")) ) { ?>
	<div class="ig_offer">
	    <a target="_blank" href="?ig_dismiss_admin_notice=1&ig_option_name=ig_offer_bfcm_done_2019"><img src="<?php echo  $this->plugin_url ?>/assets/images/bfcm-2019.png"  /></a>
	</div>

<?php } 
if( (get_option('ig_offer_last_day_bfcm_done_2019_icegram') !==  1 ) &&  ($ig_current_date >= strtotime("2019-12-02")) && ($ig_current_date <= strtotime("2019-12-03")) ) { ?>
	<div class="ig_offer">
	    <a target="_blank" href="?ig_dismiss_admin_notice=1&ig_option_name=ig_offer_last_day_bfcm_done_2019"><img src="<?php echo  $this->plugin_url ?>/assets/images/bfcm-last-2019.png"  /></a>
	</div>

<?php } ?>
