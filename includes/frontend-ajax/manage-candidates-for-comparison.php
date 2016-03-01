<?php
if(isset($_GET['candidate'])){
    /*Proper URL*/
    $raceId = Input::get('raceid');
    // Let's see what they chose so far...
    $selections = isset($_SESSION['selections'])? $_SESSION['selections'] : array();
    if(!empty($selections)){
	/* $selections already has something*/
	// Send a helper message for this race
	if(in_array($_GET['candidate'], $selections,true)){
	    /* Chosen value exists in SESSION */
	    $key = array_search($_GET['candidate'], $selections);
	    $outputArray['removed'] = $selections[$key];
	    unset($selections[$key]);

	    // Re-index the array, because it looks weird if you don't!
	    $selections = array_values($selections);
	    if(Input::get('writein')=='true'){
		$selections = array();
	    }

	}else{
	    /* Chosen value does NOT exist in $selections */
	    if(count($selections)>=4){
		/* 4 or more values exist in $selections */
		// Get rid of the first one, because we already have 4 set
		$removed = array_shift($selections);
		//$outputArray['response'] = 'Removed 1 candidate from selection';
		$outputArray['removed'] = $removed;
	    }elseif(count($selections)===1){
		// 1 in selection
		Helper::init();
		$helper = Helper::addHelper("twoCandidatesRace-$raceId", "Compare", "/compare-candidates?race=$raceId&token=".Session::$token, "You've selected two, now compare them by clicking 'Compare these two' or 'Next' below.  You may also compare up to 4 candidates at a time.");
		if($helper!==null){
		    $outputArray['helper'] = $helper;
		}


	    }
	    $selections[] = $_GET['candidate'];
	    $outputArray['added'] = $_GET['candidate'];
	    // If it's a writein...
	}
    }else{
	/* $selections is empty */
	$selections[] = $_GET['candidate'];
	$outputArray['added'] = $_GET['candidate'];
	if(Input::get('writein')=='true'){
	    $selections[] = 'writein';
	    Helper::init();
	    $helper = Helper::addHelper("oneCandidatesRace-$raceId", "Compare", "/compare-candidates?race=$raceId&token=".Session::$token, "View this candidate by clicking 'View Candidate' or 'Next' below.");
	    if($helper!==null){
		$outputArray['helper'] = $helper;
	    }

	}

    }
}
else{
    /*Improper URL*/
    $outputArray['error'] = 'You must choose a candidate';
}
$outputArray['candidates'] = !empty($selections)? $selections : null;
$_SESSION['selections'] = $selections;
