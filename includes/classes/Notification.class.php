<?php
class Notification {
    private static
	    $confirmationAction,
	    $confirmationMessage,
	    $error,
	    $failed,
	    $hasInfo,
	    $hasWarning,
	    $info,
	    $requiresConfirmation,
	    $succeeded,
	    $success,
 	    $warning;
    private static $instance = false;
    
    public static function init(){
	self::$instance = true;
	self::$succeeded = self::$failed = self::$hasInfo = self::$hasWarning = self::$requiresConfirmation = false;
	self::$info = self::$error = self::$success = self::$warning = self::$confirmationMessage = '';
	if(Session::exists('notifications')){
	    self::restoreNotifications();
	}
    }
    public static function addErrorMessage($msg){
	self::$error .= "<div>$msg</div>";
	self::$failed = true;
	self::saveInSession();
    }    
    public static function addSuccessMessage($msg){
	self::$success .= "<div>$msg</div>";
	self::$succeeded = true;
	self::saveInSession();
    }
    public static function addInfoMessage($msg){
	self::$info .= "<div>$msg</div>";
	self::$hasInfo = true;
	self::saveInSession();
    }
    public static function addWarningMessage($msg){
	self::$warning .= "<div>$msg</div>";
	self::$hasWarning = true;
	self::saveInSession();
    }
    public static function requireConfirmation($msg,$action){
	self::$confirmationMessage .= "<div>$msg</div>";
	self::$requiresConfirmation = true;
	self::$confirmationAction = $action;
	self::showConfirmationPrompt();
    }
    private static function showConfirmationPrompt(){
	get_admin_header();
	self::addErrorMessage(self::$confirmationMessage);
	self::showIfPresent();
	?><form method="post" action="">
	    <input class="btn btn-md btn-danger" style="margin: 15px 0;" name="confirm_<?php echo self::$confirmationAction ?>"  type="submit" value="Delete Product" />
	</form><?php
	get_admin_footer();
	die();
    }
    public static function getAjaxNotifications(){
	//bool, string, cssClass
	$types = array(
	    array('succeeded','success','success'),
	    array('failed','error','danger'),
	    array('hasInfo','info','info'),
	    array('hasWarning','warning','warning')
	);
	$output = '';
	foreach($types as $type){
	    $bool = self::$$type[0];
	    $string = self::$$type[1];
	    $cssClass = $type[2];
	    if($bool){
		$output .=  "<div class=\"alert alert-$cssClass\">$string</div>";
	    }
	}
	return $output;
    }
    private static function restoreNotifications(){
	$old = Session::get('notifications');
	foreach ($old as $varName => $value) {
	    self::$$varName = $value;
	}
    }
    /**
     * Stores the curent state in case of a killed script that had un-printed notifications
     * @uses Session Session class
     */
    private static function saveInSession(){
	Session::put('notifications', get_class_vars(__CLASS__));
    }
    public static function showIfPresent(){
	Session::delete('notifications');
	//bool, string, cssClass
	$types = array(
	    array('succeeded','success','success'),
	    array('failed','error','danger'),
	    array('hasInfo','info','info'),
	    array('hasWarning','warning','warning')
	);
	foreach($types as $type){
	    $bool = self::$$type[0];
	    $string = self::$$type[1];
	    $cssClass = $type[2];
	    if($bool){
		echo "<div class=\"alert alert-$cssClass\">$string</div>";
	    }
	}
	self::showAjaxArea();
    }
    private static function showAjaxArea(){
	?><div id="ajax-notifications"></div><?php
    }

}