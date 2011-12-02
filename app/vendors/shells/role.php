<?php

class RoleShell extends Shell {

	var $uses = array('Users','Aros','Acos','Queues');
	var $settings = array(
		'login' => false,
		'role' => false, // defalut users role - users
		'roleName' => false
	);
	function startup() {
		App::import('Core', 'Controller');
		App::import('Component', 'Acl');

		$this->Acl = & new AclComponent();
		$controller = null;
		$this->Acl->startup($controller);
		$this->Aro = & $this->Acl->Aro;
	}

	function main(){
		$this->out('Users managament');
		$this->hr();
		$this->out('Commands:');
		$this->out('cake role info {login}');
		$this->out('cake role change {login} {newRoleName}');
		$this->out('cake role users_aros_sync {roleName} - set default role name');
		$this->hr();
		if(isset($this->args[0])){
			$method = '_' . $this->args[0];
		}else{
			$this->err('[error] set valid command');
			exit;
		}
		if(method_exists($this, $method)){
			if(isset($this->args[1])){
				$this->settings['login'] = $this->args[1];
			}
			if(isset($this->args[2])){
				$this->settings['roleName'] = low($this->args[2]);
			}
			$this->{$method}();
		}else{
			die('[error] Set right command');
		}
	}

	function bath(){
		$this->hr();
		$this->out('Bath change');
		$this->hr();
		exit;
// 		#delete
// 		if($this->Aros->delete(5813)){
// 			$this->out('user clean');
// 		}else{
// 			$this->out('user fail');
// 		}
		// #recovery
		// $this->Users->save(array('Users' => array(
		// 			'id' => 7980,
		// 			'login' => 'dk7294',
		// 			'password' => '3cdaaa5d7386256c85c6a97cc0e10c8a',
		// 			'name' => 'Krzysztof',
		// 			'surname' => 'Kur',
		// 			'agent' => 7294,
		// 			'role_id' => 14,
		// 			'enable' => true
		// 		)));
		// 		exit;
		$data = $this->Users->find('all', array('conditions' => array('CHAR_LENGTH(Users.login) < 4', 'CHAR_LENGTH(Users.login) >= 2')));

		foreach ($data as $row){
			echo $row['Users']['login']. ':';
		}
		$this->out('found: '. count($data));
		$options = $this->in('Is ok ?:', array('y', 'n'), 'n');
		if(strtolower($options) === 'y'){
			foreach ($data as $row){
				$this->Users->create();
				$this->Users->save(array(
					'Users' => array(
						'id' => $row['Users']['id'],
						'role_id' => 6
					)
				));
				$this->out("{$row['Users']['login']} saved.");
			}
		}else{
			$this->out('canceling');
		}
		$this->out('finish');
	}

	function _info(){
		$user = $this->_getUser($this->settings['login']);
		$this->out('# Users');
		$this->out("id		login		role_id		enable		role");
		$this->out("{$user['Users']['id']}		{$user['Users']['login']}		{$user['Users']['role_id']}		{$user['Users']['enable']}		{$user['Roles']['name']}");

		$userNode = $this->_getNode($user);
		$roleNode = $this->_getNode($user['Roles']['name']);

		$this->out('# Aros (user)');
		$this->out("id		parent		model		alias");
		$this->out("{$userNode['Aro']['id']}		{$userNode['Aro']['parent_id']}		{$userNode['Aro']['model']}.{$userNode['Aro']['foreign_key']}	{$userNode['Aro']['alias']}");
		$this->out('# Aros (role)');
		$this->out("id		parent		model		alias");
		$this->out("{$roleNode['Aro']['id']}		{$roleNode['Aro']['parent_id']}		{$roleNode['Aro']['model']}.{$roleNode['Aro']['foreign_key']}		{$roleNode['Aro']['alias']}");
	}
	function _change(){
		$user = $this->_getUser($this->settings['login']);
		$this->out('# Users (old data)');
		$this->out("id		login		role_id		enable		role");
		if($user['Users']['enable'] === true){
			$enable = 'true';
		}else{
			$enable = 'false';
		}
		$this->out("{$user['Users']['id']}		{$user['Users']['login']}		{$user['Users']['role_id']}		{$enable}		{$user['Roles']['name']}");

		$userNode = $this->_getNode($user);
		$roleNode = $this->_getNode($this->settings['roleName']);
		if(!$roleNode){
			$this->err('[error] Wrong role name ! Try again.');
			exit;
		}
		if(!$userNode){
			$options = $this->in("Create new node for ({$user}):", array('y', 'n'), 'y');
			if(strtolower($options) === 'y'){
				$userNode = $this->_userSync($user, $roleNode['Aro']['foreign_key']);
			}
		}

		if(!$userNode){
			$this->err('[critical] user node fail.');
			exit;
		}

		if(empty($user) || empty($userNode) || empty($roleNode)){
			die("empty `user` or `userNode` or `roleNode`");
		}

		$this->out('# Aros (user node)');
		$this->out("id		parent		model		alias");
		$this->out("{$userNode['Aro']['id']}		{$userNode['Aro']['parent_id']}		{$userNode['Aro']['model']}.{$userNode['Aro']['foreign_key']}	{$userNode['Aro']['alias']}");

		$this->out('# Aros (role node)');
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
			$this->out('[info] User saved succes.');
		}else{
			$this->err('[error] User save fail.');
		}
		//@ todo save user node with new parent_id
	}

	function _users_aros_sync(){
		$roleName = $this->settings['login']; // ovveride
		$roleNode = $this->_getNode($roleName);
		if(!$roleNode){
			$this->err('[error] Please set valid `roleName`');
			exit;
		}
		$this->Users->Behaviors->attach('Containable');
		$data = $this->Users->find('all', array(
			'fields' => 'Users.id',
			#'conditions' => array('Users.enable' => true),
			'group' => 'Users.id, Aros.id HAVING COUNT(Aros.id) = 0',
		));
		$this->out('[info] users witchout aros (not sync): '. count($data));
		foreach($data as $row){
			$user = $this->Users->findById($row['Users']['id']);
			var_dump($user);
			$this->out('1. Refresh User and create Aros');
			$this->out('2. Delete User');
			$options = $this->in("User ({$user['Users']['login']}) not sync, what to do:", array(1, 2), 1);
			if($options == 1){
				$this->_userSync($user, $roleNode['Aro']['foreign_key']);
			}else{
				$this->Users->delete($user['Users']['id']);
			}
		}
	}

	/**
	 * Find User
	 *
	 * @return user array
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
			$node = $this->Aro->findByAlias(strtolower($name));
		}
		if(!$node){
			$this->err("Aro node by fk({$name}) not exist.");
		}
		return $node;
	}

	function _userSync($user, $roleId){
		$tmpUsers = $this->Users->findById($user['Users']['id']);
		if($tmpUsers){
			var_dump($tmpUsers);
			$this->Users->delete($user['Users']['id']);
			$tmpUsers['Users']['role_id'] = $roleId;
			if($tmpUsers['Users']['enable'] == false){
				$options = $this->in("[question] force set `enable` = true for this user ?:", array('y', 'n'), 'n');
				if(strtolower($options) === 'y'){
					$tmpUsers['Users']['enable'] = true;
				}
			}
			unset($tmpUsers['Roles']);
			unset($tmpUsers['Aros']);
			$this->Users->create($tmpUsers);
			$saveUser = $this->Users->save($tmpUsers);
			if($saveUser){
				var_dump($saveUser);
				$this->out('[ok] user row recreate');
				return $this->_getNode($user);
			}else{
				$this->err('[error] node create fail, try again');
				exit;
			}
		}else{
			$this->err('[error] critical user not found');
		}
	}
	protected function _paginate($list) {
		if (count($list) > 20) {
			$chunks = array_chunk($list, 10);
			$chunkCount = count($chunks);
			$this->out(implode("\n", array_shift($chunks)));
			$chunkCount--;
			while ($chunkCount && null == $this->in('[info] Press <return> to see next 10 files')) {
				$this->out(implode("\n", array_shift($chunks)));
				$chunkCount--;
			}
		} else {
			$this->out(implode("\n", $list));
		}
	}
}

?>