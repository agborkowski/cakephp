<?php

class RoleShell extends Shell {

	var $uses = array('Users');
	var $settings = array(
		'login' => array(),
		'role' => 7 // defalut users role
	);
	function startup() {
		App::import('Core', 'Controller');
		App::import('Component', 'Acl');

		$this->Acl = & new AclComponent();
		$controller = null;
		$this->Acl->startup($controller);
		$this->Aro = & $this->Acl->Aro;
	}

	function main() {
		$this->out('Aro chnage user role');
		$this->hr();
		if(method_exists($this, isset($this->args[0]))){
			$this->{$this->args[0]};
		}else{
			die('set right command');
		}
	}

	function bath(){
		$data = $this->Users->find('all', array('conditions' => array('Users.enable' => true)));
		$this->out('found: '. count($data));
		foreach ($data as $row){
			$this->Users->create();
			$this->Users->save(array(
				'Users' => array(
					'id' => $row['Users']['id'],
					'role_id' => 14
				)
			));
		}
	}

	function change(){
		if(empty($this->args[0]) || empty($this->args[1])){
			die("invalid command ! usage `cake role {existUserLogin} {newRolname}`");
		}

		$this->settings['login'] = $this->args[0];
		$this->settings['role'] = $this->args[1];

		$user = $this->_getUser($this->settings['login']);
		$this->out('# Users');
		$this->out("id		login		role_id		enable		role");
		$this->out("{$user['Users']['id']}		{$user['Users']['login']}		{$user['Users']['role_id']}		{$user['Users']['enable']}		{$user['Roles']['name']}");

		$userNode = $this->_getNode($user);
		$roleNode = $this->_getNode($this->settings['role']);

		if(empty($user) || empty($userNode) || empty($roleNode)){
			die("invalid command 2 ! usage `cake role {existUserLogin} {newRolname}`");
		}

		$this->out('# Aros (user)');
		$this->out("id		parent		model		alias");
		$this->out("{$userNode['Aro']['id']}		{$userNode['Aro']['parent_id']}		{$userNode['Aro']['model']}.{$userNode['Aro']['foreign_key']}	{$userNode['Aro']['alias']}");

		$this->out('# Aros (role)');
		$this->out("id		parent		model		alias");
		$this->out("{$roleNode['Aro']['id']}		{$roleNode['Aro']['parent_id']}		{$roleNode['Aro']['model']}.{$roleNode['Aro']['foreign_key']}		{$roleNode['Aro']['alias']}");

		$this->Users->id = $user['Users']['id'];
		$save = $this->Users->save(array(
			'Users' => array(
				'id' => $user['Users']['id'],
				'role_id' => $roleNode['Aro']['foreign_key']
			)
		));
		if($save){
			$this->out('User saved');
		}else{
			$this->err('User save failed');
		}
		//@ todo save user node with new parent_id
	}
	/**
	 * Find User
	 *
	 * @return int user id
	 */
	function _getUser($login){
		$user = $this->Users->findByLogin($login);
		if(!$user){
			die("User login ({$login}) not found.");
		}
		return $user;
	}

	/**
	 * Find Aro node by userId
	 *
	 * @return int role id
	 */
	function _getNode($name){
		if(is_array($name)){
			$node = $this->Aro->findByForeignKey($name['Users']['id']);
			$name = "{$name['Users']['login']}[{$name['Users']['id']}]";
		}else{
			$node = $this->Aro->findByAlias($name);
		}
		if(!$node){
			die("Aro node by fk({$name}) not exist.");
		}
		return $node;
	}
}

?>