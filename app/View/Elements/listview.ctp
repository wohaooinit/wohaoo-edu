<script type="text/javascript">
	window.__dropDownClick = function(widget){
		dojoQuery("a", widget.domNode).forEach(function (link){
			window.location = link.href.toString();
		});
	};
</script>
<?php
	/**
	litsview => { url,
			     base_url,
			     title,
			     add_url,
			     folder_service_url,
			     move_to_folder_url,
			     delete_url,
			     name,
			     model,
			     show_headers,
			     $actions => {caption, target},
			     item_actions => {caption, target},
			     default_item_action = {caption, target},
			     items => {
			     			status,
			     			caption,
			     			description (html)
			     			stats => {caption, value}
			     		   },
			     filters => {
			     			caption,
			     			name,
			     			options => {autoCompleteUrl}  OR [{name, value}] 
			     		   }
			      folders => {
			     			caption,
			     			size,
			     			id
			     		   }
			     paging
	 */
?>
<?php
	if(!isset($use_folders)){
		$use_folders = true;
	}
	if(!$use_folders)
		$use_folders = false;
		
	if(!isset($can_delete)){
		$can_delete = true;
	}
	if(!$can_delete)
		$can_delete = false;
	
	if($show_headers){
?>
<div class="listview" 
         data-dojo-type="fn/listview/ListView"
         data-dojo-props="url:'<?php echo $url;?>', baseUrl:'<?php echo $base_url;?>', moveToFolderUrl:'<?php echo $move_to_folder_url;?>',
                deleteUrl:'<?php echo $delete_url;?>', title:'<?php echo $title;?>', name:'<?php echo $name;?>', useFolders:'<?php echo $use_folders;?>' ">
	<div class="content">
		<div class="lastUnit header size1of1">
			<h1 class="inline-block"><a href="<?php echo $url;?>"><?php echo $title;?></a></h1>
			<div id="listview-actions" 
			         data-dojo-type="dijit/form/ComboButton" 
			         class="listview-actions button p0 inverse"
			         data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
				<span><a href="<?php echo $add_url;?>"><?php echo __("Add") . " " . $name;?></a></span>
				<div data-dojo-type="dijit/DropDownMenu">
					<?php
						foreach($actions as $action){
							$action_caption = $action['caption'];
							$action_target = $action['target'];
					?>
					<div data-dojo-type="dijit/MenuItem" data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
						<a href="<?php echo $action_target;?>"><?php echo $action_caption;?></a>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
		<div class="button-bar group section">
			<div class="lastUnit unit">
				<div  class="cbx0 listViewSelectAll"
					data-dojo-type="dijit/form/Checkbox"
				        data-dojo-attach-event="onChange:_onSelectAllChange"></div>
				<?php
				if($use_folders){
				?>
				<div  class="dataStore"
					data-dojo-id="folderItemtore" data-dojo-type="dojo/store/Memory"
					data-dojo-props="data: [
						<?php
							foreach($folders as $folder){
								$folder_caption = $folder['caption'];
								$folder_id = $folder['id'];
								$folder_size = $folder['size'];
						?>
						{ caption:'<?php echo $folder_caption;?>', size:<?php echo $folder_size;?>, folderId:<?php echo $folder_id;?>},
						<?php
							}
						?>
					]"></div>
				<div  id="folderbutton"
					class="folderbutton"
					data-dojo-type="fn/listview/FolderButton" 
				        data-dojo-attach-point="listViewFolderButton"
				        data-dojo-attach-event="onSelectFolder:_onSelectFolder"
				        data-dojo-props="folderItemStore: folderItemtore, serviceUrl:'<?php echo $folder_service_url;?>', caption: '<?php echo __("Folders");?>', model: '<?php echo $model;?>'">
				</div>
				<?php
				}
				?>
				<div class="dataStore" data-dojo-id="filterItemStore" data-dojo-type="dojo/store/Memory"
					data-dojo-props="data: [
						<?php
							foreach($filters as $filter){
								$filter_caption = $filter['caption'];
								$filter_name = $filter['name'];
								$filter_options = isset($filter['options'])?$filter['options']: array();
						?>
						{ caption:'<?php echo $filter_caption;?>', name:'<?php echo $filter_name;?>',
						   options: <?php
						   	if(isset($filter_options['auto_complete_url'])){
						   		$autocomplete_url = $filter_options['auto_complete_url'];
						   		echo "{autoCompleteUrl:'${autocomplete_url}'}";
						   	}else{
						   		echo "[";
						   		foreach($filter_options  as $data => $value){
						   			echo "{value: '$value', data: '$data'},";
						   		}
						   		echo "]";
						   	}
						   ?>
						},
						<?php
							}
						?>
					]"></div>
				<div  class="button filterbutton"
					id="filterbutton"
					data-dojo-type="fn/listview/FilterButton"
				        data-dojo-attach-point="listViewFilterButton"
				        data-dojo-attach-event="onSelectFilter:_onSelectFilter"
				        data-dojo-props="filterItemStore: filterItemStore, caption:'<?php echo __("Filter");?>'">
				</div>
				<?php
				if($can_delete){
				?>
				<div  class="button danger"
					data-dojo-type="dijit/form/Button"
				        data-dojo-attach-point="listViewDeleteButton"
				        data-dojo-attach-event="onClick:_onDelete"
				        data-dojo-props="label:'<?php echo __("Delete");?>'"></div>
				<?php
				}
				?>
				<?php
				if($use_folders){
				?>
				<div
					id="movetofolderbutton"
					class="button listview-move-button movetofolderbutton"
					data-dojo-type="fn/listview/MoveToFolderButton"
					data-dojo-props="folderItemStore: folderItemtore, caption:'<?php echo __("Move To");?>'"
					data-dojo-attach-event="onSelectFolder:_onMoveToFolder">
				</div>
				<?php
				}
				?>
			</div>
		</div>
		<div class="size1of1 lastGroup section stickerContainer" 
			data-dojo-type="dijit/layout/ContentPane"
		 	 data-dojo-attach-point="stickerContainer">
		 </div>
		<div class="size1of1 lastGroup section itemContainer" 
			data-dojo-type="dijit/layout/ContentPane"
		 	 data-dojo-attach-point="itemContainer">
		 <?php
		}
		?>
			<ul class="listview-item-contaier">
				<?php
					$default_item_action_target = $default_item_action['target'];
					$default_item_action_caption = $default_item_action['caption'];
					foreach($items as $item){
						$id = $item['id'];
						$item_status = $item['status'];
						$item_caption = $item['caption'];
						//$item_caption = $this->Format->text($item_caption, 50);
						$item_description = $item['description'];
						$item_stats = $item['stats'];
						$n_stats = count($item_stats);
						$n = $n_stats + 5;
				?>
				<li class="listview-item selfclear relative listViewItems"
				      data-dojo-type="fn/listview/ListViewItem"
				      data-dojo-props="itemId:<?php echo $id;?>">
					<div class="meta unit size3of4">
						<div class="unit size5of<?php echo $n;?> nopadding">
							<input data-dojo-type="dijit/form/CheckBox" 
							        class="listview-select-item cbx0 listViewCheckBoxGroup"
							        data-dojo-attach-event="onChange:_onItemSelectChange"
							        data-dojo-props="itemId:<?php echo $id;?>"/>
							<?php
								if(!is_array($item_status)){
									$item_status_class= $item_status;
									$item_status_content = "";
								}else{
									$item_status_class = isset($item_status['class'])?$item_status['class']:"";
									$item_status_content = isset($item_status['content'])?$item_status['content']:"";
								}
							?>
							<span class="listview-item-status-icon <?php echo $item_status_class;?>"><?php echo $item_status_content;?></span>
							<div class="listview-item-content">
								<h4 class="listview-item-header">
									<a href="<?php echo sprintf($default_item_action_target, $id);?>"><?php echo h($item_caption);?></a>
								</h4>
								<div class="listview-item-description">
									<?php echo $item_description;?>
								</div>
							</div>
						</div>
						<div class="lastUnit size<?php echo $n_stats;?>of<?php echo $n;?> listview-stats">
							<?php
								foreach($item_stats as $item_stat){
									$item_stat_name = $item_stat['caption'];
									$item_stat_value = $item_stat['value'];
							?>
								<div class="data inline-block">
									<p><?php echo $item_stat_name;?></p>
									<h4><?php echo $item_stat_value;?></h4>
								</div>
							<?php
								}
							?>
						</div>
					</div>
					<div class="meta-actions lastUnit size1of4">
						<div  id="listviewitem<?php echo $id;?>"
							data-dojo-type="dijit/form/ComboButton" 
							class="listview-item-actions button inverse"
							data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
							<?php
								$default_item_action_target = $default_item_action['target'];
								$default_item_action_caption = $default_item_action['caption'];
							?>
							<span><a href="<?php echo sprintf($default_item_action_target, $id);?>"><?php echo $default_item_action_caption;?></a></span>
							<div data-dojo-type="dijit/DropDownMenu">
								<?php
									foreach($item_actions as $item_action){
										$item_action_caption = $item_action['caption'];
										$item_action_target = $item_action['target'];
										$item_action_target = sprintf($item_action_target, $id);
								?>
								<div data-dojo-type="dijit/MenuItem" data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
									<a href="<?php echo $item_action_target;?>"><?php echo $item_action_caption;?></a>
								</div>
								<?php
									}
								?>
							</div>
						</div>
					</div>
				</li>
				<?php
					}
				?>
			</ul>
			<?php
			if(isset($paging)){
			?>
			<div class="paging">
				<?php 
					$this->log("paging=" . var_export($paging, true), 'debug');
					if(!isset($paging['prev']))
						$paging['prev'] = "";
					echo $paging['prev'];
						
					if(!isset($paging['numbers']))
						$paging['numbers'] = "";
					echo $paging['numbers'];
					
					if(!isset($paging['next']))
						$paging['next'] = "";
					echo $paging['next'];
				?>
			</div>
			<?php
			}
			if($show_headers){
			?>
		</div>
		<div class="lastUnit header size1of1">
			<h1 class="inline-block"><a href="<?php echo $url;?>"><?php echo $title;?></a></h1>
			<div id="listview-actions-2" 
			         data-dojo-type="dijit/form/ComboButton" 
			         class="listview-actions button p0 inverse"
			         data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
				<span><a href="<?php echo $add_url;?>"><?php echo __("Add") . " " . $name;?></a></span>
				<div data-dojo-type="dijit/DropDownMenu">
					<?php
						foreach($actions as $action){
							$action_caption = $action['caption'];
							$action_target = $action['target'];
					?>
					<div data-dojo-type="dijit/MenuItem" data-dojo-props='onClick: function(e){ __dropDownClick(this);}'>
						<a href="<?php echo $action_target;?>"><?php echo $action_caption;?></a>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
			<?php
			}
			?>