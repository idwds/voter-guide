<?php
/**
 * Here we include a script in the /includes/frontend-ajax/ folder that has the name of the ?action= parameter.  The script included
 * needs to use this $outputArray var without outputting anything in the script itself, because this file sends the headers
 * and a JSON encoded version of the $outputArray.
 */
$outputArray = array();
// We don't want them including files outside of this folder; hence the ".." filtering.
if(isset($_REQUEST['action'])&&  strpos($_REQUEST['action'], '..')===false):
    $action = $_REQUEST['action'];
    $filename = $GLOBALS['inc']."frontend-ajax/$action.php";
    if(is_file($filename)){
	if(!$GLOBALS['debug']){
	    ob_start();
	}
	require_once ($filename);	
	if(!$GLOBALS['debug']){
	    ob_end_clean();
	}
    }
    else{
	$outputArray['error'] = 'Invalid action!';
    }
else:
    $outputArray['error'] = 'Invalid action!';
endif;

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($outputArray);