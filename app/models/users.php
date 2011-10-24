<?php
class Users extends AppModel {
	var $name = 'Users';
	var $actsAs = array('Acl' => array('type' => 'requester'));
	var $belongsTo = array(
		'Roles' => array(
			'foreignKey' => 'role_id'
		)
	);
	var $table = 'users';

	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		if (isset($this->data['Users']['role_id'])) {
			$groupId = $this->data['Users']['role_id'];
		} else {
			$groupId = $this->field('role_id');
		}
		if (!$groupId) {
			return null;
		} else {
			return array('Roles' => array('id' => $groupId));
		}
	}
}
?>