<?php

Notification::init();
if(Input::get('saved','get')==''){
    $location = Input::get('street_address').', '.Input::get('city').', '.Input::get('state').' '.Input::get('zip');

    $f_name = Input::get('f_name');
    $l_name = Input::get('l_name');
    if(!empty($f_name)||!empty($l_name)){
        User::record('f_name', $f_name);
        User::record('l_name', $l_name);
    }
    if(Input::has('email')){
        if(filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
            User::record('email', Input::get('email'));
            // We don't need to announce this--hence commented:
			// Notification::addSuccessMessage("Your email address has been recorded.");
        }else{
            Notification::addErrorMessage("That email is not valid!");
        }
    }elseif(!User::hasEmail()){
        Notification::addErrorMessage("You need to enter your email address.");
    }

    if(empty($location)||  strlen($location)<5){
        Notification::addErrorMessage("You need to specify an address.");
        $queryString = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : 'token='.Session::$token;
        Redirect::to("/enter-address?$queryString");
    }
    if(!User::hasEmail()){
        $queryString = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : 'token='.Session::$token;
        Redirect::to("/enter-address?$queryString");
    }
    Geocode::processAddress($location);
    // Sort of a hack to bring these vars into this scope...
    foreach(Geocode::$publicVars as $varname){
        $$varname = Geocode::$$varname;
    }
    User::record('address', $formatted_address);
    if($state_code!==getOption('state')){
        Notification::addErrorMessage("Sorry--this guide is only for the state of ".getOption('state')."!");
        $queryString = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : 'token='.Session::$token;
        Redirect::to("/enter-address?$queryString");
    }
    include($GLOBALS['inc'].'districts-from-geo.php');
    $_SESSION['congress_district'] = $congress_district;
    $_SESSION['lower_district'] = $lower_district;
    $_SESSION['upper_district'] = $upper_district;
    $_SESSION['bese_district'] = $bese_district;
    $_SESSION['statefp'] = $statefp;
    $_SESSION['county_id'] = $county_id;
    if(isset($_SESSION['selections']))
        unset($_SESSION['selections']);
    if (!empty($state_code)&&
        !empty($lower_district)&&
        !empty($upper_district)&&
        !empty($bese_district)&&
        !empty($congress_district)) {
        $ballot = new Ballot($state_code,$lower_district,$upper_district,$bese_district,$congress_district);
        Session::put('ballot', serialize($ballot));
    }
}
ob_start();
?>
<style>
html body{margin: 0 12px;}
#content{font-family: Georgia; padding: 20px 60px;min-height: 100px;}
.mainBG{background: #f2f2f2;}
</style>
<?php
$GLOBALS['page']->head = ob_get_clean();
$page->title = "Confirm your address";

get_header() ?>
    <div id="content" class="mainBG">
    <?php Notification::showIfPresent(); ?>
	<div class="row">
	  <div class="col-lg-12">
              <h3>Confirm your address</h3>
	      <form action="" method="get" class="form-inline">
		<div class="form-group">
                    <label for="street_address">Street Address</label>
		    <input type="text" id="street_address" name="street_address" class="form-control" value="<?php echo htmlentities(Input::get('street_address'), ENT_QUOTES) ?>" placeholder="Enter street address">
                </div>
		<div class="form-group">
                    <label for="city">City</label>
                  
		    <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlentities(Input::get('city'), ENT_QUOTES) ?>" placeholder="Enter city">
                </div>
		<div class="form-group">
                    <label for="zip">Zip</label>
		    <input type="text" id="zip" name="zip" class="form-control" value="<?php echo htmlentities(Input::get('zip'), ENT_QUOTES) ?>" placeholder="Enter zip">
                </div>
		    <?php /*<input type="text" name="location" class="form-control" value="<?php echo htmlentities(Input::get('location'), ENT_QUOTES) ?>" placeholder="Enter street address, city, ZIP code"> */?>
		    <input name="token" value="<?php echo Session::$token ?>" type="hidden" />
		    <div class="input-group-btn">
			<button type="submit" class="btn btn-default">Search </button>
			<?php if(getOption('allow-use-location')){
			    ?><a href="/get-location?token=<?php echo Session::$token ?>" class="btn btn-default" title="Use my location"><span class="glyphicon glyphicon-map-marker"></span></a><?php
			} ?>
		    </div><!-- /btn-group -->
	      </form>
            </div><!-- /form-group -->
	  </div><!-- /.col-lg-6 -->
	</div><!-- /.row -->
	<div class="row" style="margin-top:20px;margin-bottom: 20px;">
	    <div class="col-sm-6">
		<strong>Found Address:</strong>
		<?php echo $formatted_address ?>
		<br /><?php echo $state_code=='LA'? 'Parish': 'County'?>: <?php echo $county; ?>
		<?php if(Geocode::$yahoo): ?>
		<div id="yahoo-attribution">
		    Geocoding created with <a href="http://developer.yahoo.com/search/boss/" target="_blank"><img src="/images/ysb_v1a.png" width="164" height="20" alt="Yahoo! Boss" /></a>
		</div>
		<?php endif; ?>
	    </div>
	    <div class="col-sm-6">
		<div id="districts"><strong>Your Districts:</strong> <br />
			Congressional: <?php echo $congress_district ?><br />
			State Upper: <?php echo $upper_district ?><br />
			State Lower: <?php echo $lower_district ?></div>
			BESE: <?php echo $bese_district ?></div>
		<a href="/show-districts?token=<?php echo Session::$token ?>" class="btn btn-default">Change Districts</a>
		<?php $nextUrl = (getOption('primary-mode')? '/choose-races' : '/ballot') ?>
		<a href="<?php echo $nextUrl ?>?token=<?php echo Session::$token ?>" class="btn btn-primary">Confirm Districts and Continue</a>
	    </div>
	</div>
    </div>
    <div style="overflow-x: hidden;">
	<?php $address = str_replace(' ', '+', $formatted_address); ?>
	<iframe src="https://maps.google.com/maps
?f=q
&amp;source=s_q
&amp;hl=en
&amp;geocode=
&amp;q=<?php echo $address; ?>
&amp;aq=0
&amp;ie=UTF8
&amp;hq=
&amp;hnear=<?php echo $address; ?>
&amp;t=m
&amp;ll=<?php echo $latitude; ?>,<?php echo $longitude; ?>
&amp;z=12
&amp;iwloc=
&amp;output=embed" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" width="100%" height="450"></iframe>
    </div>
<?php get_footer();