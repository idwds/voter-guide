<?php
if(Input::has('id')){
    Helper::init(); // It's only initiated in header.php
    Helper::dismissHelper(Input::get('id'));
}else{
    /*Improper URL*/
    $outputArray['error'] = 'Helper ID was not supplied.';
}
