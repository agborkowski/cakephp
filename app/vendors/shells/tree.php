<?php
class TreeShell extends Shell {
	var $uses = array ('Aros', 'Acos', 'Permissions');
	/**
	 * Verbose mode
	 *
	 * @var boolean
	 * @access public
	 */
	var $verbose = false;
	
	/**
	 * Quiet mode
	 *
	 * @var boolean
	 * @access public
	 */
	var $quiet = false;
	
	/**
	 * Startup
	 *
	 * @access public
	 * @return void
	 */
	function startup() {
		$this->verbose = isset ( $this->params ['verbose'] );
		$this->quiet = isset ( $this->params ['quiet'] );
		parent::startup ();
	}
	
	/**
	 * Welcome
	 *
	 * @access protected
	 * @return void
	 */
	function _welcome() {
		$this->hr ();
		$this->out ( 'Repair Shell' );
		$this->hr ();
	}
	
	/**
	 * Main
	 *
	 * @access public
	 * @return void
	 */
	function main() {
		$this->_welcome();

		$this->out ( '1 .Recovery the tree by `parent_id`' );
		$this->out ( '2 .Recovery the tree by `lft` & `rght` keys' );
		$this->out ( '3. Verify tree' );

		$action = $this->in ( __ ( 'What would you like to do?', true ), array (1,2,3,'Q' ), 'q' );
		$model = $this->in ( __ ( 'Model', true ), $this->uses, 'q' );

		switch ($action){
			case 1:
				$data = $this->{$model}->recover('parent');
			break;
			case 2:
				$data = $this->{$model}->recover('tree');
			break;
			case 3:
				$data = $this->{$model}->verify();
			break;
			case 'Q':
				$this->quit();
		};
		$this->process($data);
		$this->main ();
	}

	function process($data) {
		var_dump($data);
		exit;
		
		$this->hr ();
		//Print out each order's information
		$fields = array_keys($data[0]['Photo']);
		foreach ( $data as $row ) {
			$out = '';
			foreach($fields as $id => $field){
				$out .= "#{$field}|".$row['Photo'][$field]."\n";
			}
			$this->out ( $out );
		}
		$this->hr ();
		$this->out ( 'Total:' . count ( $data ) );
	}
}
?>