<?php
	App::uses('BaseLog', 'Log/Engine');

	class DatabaseLog extends BaseLog {
		public function __construct($options = array()) {
			parent::__construct($options);
		}

		public function write($type, $message) {
			// write to the database.
		}
	}
?>