<?php
class Roles extends AppModel {
	var $name = 'Roles';
	var $table = 'roles';
	var $actsAs = array('Acl' => array('type' => 'requester'));
	function parentNode() {
		return null;
	}	
}
?>