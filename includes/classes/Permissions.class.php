<?php
class Permissions{
    /**
     * Sends them to the home page and tells them they ain't allowed if they aren't one of these roles.
     * @param array $roles Roles to allow (admin,research_admin,etc.)
     * @uses AdminNotification
     * @uses Redirect
     */
    public static function allowOnlyTheseRoles($roles){
	if(!in_array($GLOBALS['user']->role, $roles)){
	    AdminNotification::addErrorMessage("You don't have permission to access that page (".$GLOBALS['page']->url.").");
	    Redirect::to('/');
	}
    }
}