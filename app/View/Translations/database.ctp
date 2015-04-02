(function(global){
<?php
	$this->layout = 'empty';
?>
TRANSLATIONS_DB = [];
<?php
	echo "\r\n";
	$this->log("translations=" . var_export($translations, true), 'debug');
	foreach($translations as  $key => $translation){
		$t9n_guid = $translation['Translation']['t9n_guid'];
		$t9n_trans_lang = $translation['Translation']['t9n_trans_lang'];
		$t9n_trans_text = $translation['Translation']['t9n_trans_text'];
?>
if(typeof TRANSLATIONS_DB['<?php echo $t9n_guid; ?>'] === 'undefined')
	TRANSLATIONS_DB['<?php echo $t9n_guid; ?>'] = [];
<?php
	echo "\r\n";
?>
TRANSLATIONS_DB['<?php echo $t9n_guid; ?>']['<?php echo $t9n_trans_lang; ?>'] = '<?php echo h($t9n_trans_text);?>';

<?php
	echo "\r\n";
?>
<?php
	}
?>
global.TRANSLATIONS_DB = TRANSLATIONS_DB;

})(this);