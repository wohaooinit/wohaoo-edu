<?php
//sidebar
$this->start('sidebar');
echo $this->element('sidebar', array(
	'name' => 'people-menu',
	'items' => array(
		__('Search') => array(
			'icon_set' => 'gn-icon',
			'icon' => 'gn-icon-search',
			'type' => 'search'
		),
		__('List') => array(
			'icon_set' => 'fa-icon',
			'icon' => 'fa-icon-list',
			'target' => $this->request->here
		),
		__('New Person') => array(
			'icon_set' => 'fn-icon',
			'icon' => 'fn-icon-person',
			'target' => $this->Html->url(array('action' => 'add'))
		)
	)
)); 
$this->log("sidebar is ready.", 'debug');
$this->end('sidebar');
?>
<?php echo $this->element('../people/table', array('show_headers' => true));?>