<?php
/* Queue Fixture generated on: 2011-11-29 09:56:51 : 1322560611 */
class QueueFixture extends CakeTestFixture {
	var $name = 'Queue';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'asterisk_id' => array('type' => 'integer', 'null' => false),
		'classification_indicator_id' => array('type' => 'integer', 'null' => true),
		'bound_type_id' => array('type' => 'integer', 'null' => false),
		'deleted' => array('type' => 'boolean', 'null' => false),
		'active' => array('type' => 'boolean', 'null' => false),
		'applications' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'SLvalue' => array('type' => 'integer', 'null' => true, 'default' => '20'),
		'wrapup_time' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id'), 'idx_queues' => array('unique' => false, 'column' => 'asterisk_id'), 'idx_queues_0' => array('unique' => false, 'column' => 'classification_indicator_id'), 'idx_queues_1' => array('unique' => false, 'column' => 'bound_type_id'), 'idx_queues_2' => array('unique' => false, 'column' => 'asterisk_id'), 'idx_queues_3' => array('unique' => false, 'column' => 'id')),
		'tableParameters' => array()
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'asterisk_id' => 1,
			'classification_indicator_id' => 1,
			'bound_type_id' => 1,
			'deleted' => 1,
			'active' => 1,
			'applications' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'SLvalue' => 1,
			'wrapup_time' => 1
		),
	);
}
