<?php

App::uses('EduController', 'Controller');
/**
 * AttributesController
 *
 * @property Attribute $Attribute
 */
class AttributesController extends  EduController {
	/**
	 *View all the attributes related to a model instance
	 *as a table
	 */
	public function table($model = '', $object_id = 0){
		$this->log("<table model=$model object_id=${object_id}", 'debug');
		try{
			$this->loadModel('AttributeDefinition');
			$find_options = array();
			//$find_options['fields'] = array('DISTINCT Attribute.att_property_name', 'Definition.atd_index');
			//$find_options['conditions'] = array('Attribute.att_class_name' => $model,
											//'Attribute.att_object_id' => $object_id);
			//$find_options['joins'] = $this->Attribute->joins;
			//$find_options['order'] = array('Definition.atd_index' => 'ASC');
		
			/*$attribute_names = $this->Attribute->find('all',
				$find_options
			);*/
			$find_options['conditions'] = array('AttributeDefinition.atd_class_name' => $model);
			$find_options['order'] = array('AttributeDefinition.atd_index' => 'ASC');
		
			$this->log("loading attribute definitions ...", 'debug');
			$attribute_names = $this->AttributeDefinition->find('all', $find_options);
			$this->log("attribute_names=" . var_export($attribute_names, true), 'debug');
		
			$this->log("loading attributes ...", 'debug');
			$attributes = $this->findAttributes($attribute_names, $model, $object_id);
		
			if(!$attributes || empty($attributes) || count($attributes) != count($attribute_names) ){
				$this->log("no attribute found. Importing attributes from table ...", 'debug');
				$this->import($model, $object_id);
				$attributes = $this->findAttributes($attribute_names, $model, $object_id);
			}
			$this->log("attributes=" . var_export($attributes, true), 'debug');
		
			$this->log("</table>");
			if(!empty($this->request->params['requested']))
				return $attributes;//request Action method call
			$this->set('attributes', $attributes);
		}catch(Exception $e){
			$this->log($e->getMessage());
			throw $e;
		}
	}
	
	public function edit($id = null) {
		$this->Attribute->id = $id;
		$db = $this->Attribute->getDataSource();
		$db->begin();
		$error = "";
		$message = "";
		$redirect = "";
		try{
			if (!$this->Attribute->exists()) {
				$error = __('Invalid Attribute');
				throw new NotFoundException($error);
			}
		
			$find_options = array();
			$find_options['fields'] = $this->Attribute->fields;
			$find_options['joins'] = $this->Attribute->joins;
			$find_options['conditions'] = array('Attribute.id' => $id);
			$attribute = $this->Attribute->find('first', $find_options);
		
			if(!$attribute['Definition']['atd_is_editable']){
				$error = __('Attribute cannt be edited');
				throw new Exception($error);
			}
		
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->Attribute->read();
				$atd_max_revisions = $attribute['Definition']['atd_max_revisions'];
				$att_serie_num = $this->Attribute->data['Attribute']['att_serie_num'];
				
				if($att_serie_num >= $atd_max_revisions){
					$error = __('The maximum number of revisions has been reached. Please, contact the site administrator');
					throw new Exception($error);
				}
				$this->log("att_serie_num=${att_serie_num}, atd_max_revisions=${atd_max_revisions}", 'debug');
				$remaining_revisions = $atd_max_revisions - $att_serie_num - 1;
				if($attribute['Definition']['atd_is_date']){
					$date = new DateTime();
					$att_content = $this->request->data['Attribute']['att_content'];
					$year = $att_content['year'];
					$month = $att_content['month'];
					$day = $att_content['day'];
					$date->setDate($year, $month, $day);
					$att_content = $date->getTimestamp();
					$this->request->data['Attribute']['att_content'] = $att_content;
				}
				//update the att_updated field
				$date = new DateTime();
				$now = $date->getTimestamp();
				$data['att_updated'] = $now;
				
				//increase revision number
				$this->request->data['Attribute']['att_serie_num'] = $att_serie_num + 1;
				//security check
				if(isset($this->request->data['Attribute']['att_class_name']) &&
					$this->request->data['Attribute']['att_class_name'] !== $this->Attribute->data['Attribute']['att_class_name']){
					$error = __("Security Error: invalid data");
					throw new Exception($error);	
				}
				if(isset($this->request->data['Attribute']['att_property_name']) &&
					$this->request->data['Attribute']['att_property_name'] !== $this->Attribute->data['Attribute']['att_property_name']){
					$error = __("Security Error: invalid data");
					throw new Exception($error);	
				}
				if(isset($this->request->data['Attribute']['att_object_id']) &&
					$this->request->data['Attribute']['att_object_id'] !== $this->Attribute->data['Attribute']['att_object_id']){
					$error = __("Security Error: invalid data");
					throw new Exception($error);	
				}
				if (!$this->Attribute->save($this->request->data)) {
					$error = __('The Attribute could not be saved. Please, try again.');
					throw new Exception($error);
				}
				//save Model data automatically
				$this->Attribute->read();
				$Model = $this->Attribute->data['Attribute']['att_class_name'];
				$ObjectId = $this->Attribute->data['Attribute']['att_object_id'];
				$AttributeName = $this->Attribute->data['Attribute']['att_property_name'];
				$Content = $this->Attribute->data['Attribute']['att_content'];
				
				if(!$this->loadModel($Model)){
					$error = __("$Model is invalid");
					throw new Exception($error);
				}
				$this->$Model->id = $ObjectId;
				if(!$this->$Model->read()){
					$error = __("$Model is invalid");
					throw new Exception($error);
				}
				$this->$Model->data[$Model][$AttributeName] = $Content;
				if(!$this->$Model->save($this->$Model->data)){
					$error = __("Unable to save $Model");
					throw new Exception($error);
				}
				
				$this->Session->setFlash(
						__("The Attribute has been saved. You have  %s possible revisions remaining.", $remaining_revisions), 
						'default', array(), 'good');
				
				$model = $this->Attribute->data['Attribute']['att_class_name'];
				$id = $this->Attribute->data['Attribute']['att_object_id'];
				$redirect = $this->Session->read('Url.referer');
				if(!$redirect)
					$redirect = Router::url(array('action' => 'table', $model, $id));
			} else {
				$this->Session->write('Url.referer', $this->referer());
				$attribute = $this->Attribute->find('first', $find_options);
				$this->request->data = $attribute;
				$this->set('attribute', $attribute);
			}
			$db->commit();
			if($redirect)
				$this->redirect($redirect);
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			if(!$error)
				$error = AppModel::$DEFAULT_ERROR_MESSAGE;
			$this->Session->setFlash($error, 'default', array(), 'bad');
		}
	}
	
	private function findAttributes($attribute_names = array(), $model = "", $object_id = 0){
		$this->log("<attributes.findAttributes model=$model object_id=${object_id}>", 'debug');
		$attributes = array();
		
		foreach($attribute_names as $attribute_name){
			//$this->log("processing attribute name:" . $attribute_name, 'debug');
			$find_options = array();
			$find_options['joins'] = $this->Attribute->joins;
			$find_options['fields'] = $this->Attribute->fields;
			$find_options['order'] = array('Attribute.att_created' => 'DESC');
			$find_options['conditions'] = array('Attribute.att_class_name' => $model,
											'Attribute.att_object_id' => $object_id,
											'Attribute.att_property_name' => 
												$attribute_name['AttributeDefinition']['atd_property_name']);
			$attribute = $this->Attribute->find('first', $find_options);
			if($attribute)
				$attributes[] = $attribute;
		}
		$this->log("attributes:=" . var_export($attributes, true), 'debug');
		$this->log("</attributes.findAttributes>", 'debug');
		return $attributes;
	}
	
	/**
	 *Update or create a new Attribute object.
	 *The attribute can be part of a dataset or not.
	 *Datasets are logical aggregations of Attributes.
	 *All Attribute of the same Dataset have the same Dataset Id.
	 *Attributes of the same Dataset arenot forcibly of the same object (Object Id),
	 *or of the same model (Class Name).
	 *@param $class_name, the name of the model ('User', 'Project', etc).
	 *@param $object_id, The Id of the object of which this Attribute belongs (can be null)
	 *@param $property_name, the name of the Attribute ('email', 'id', etc)
	 *@param $content, the value of this Attribute
	 *@param $dataset_id, the id of the Dataset to which this Attribute belongs, if no dataset id is specified
	 *	a new dataset id will be generated
	 *@returns the  id of the Dataset to which this Attribute belongs, if everything goes fine, false otherwise.
	 *@throws Exception, if arguments are invalid or if an error occurred during database operations 
	 */
	public function update($class_name = '', $object_id = '', $property_name = '', $content = '', $dataset_id = ''){
		$error = '';
		$this->log("<Attribute.update class_name=${class_name} object_id=${object_id} " .
					"property_name=${property_name} content=$content dataset_id=${dataset_id}", 'debug');
		if(empty($this->request->params['requested'])){
			$this->log("</Attribute.update ret=false reason='Only requested action are accepted'>", 'debug');
			$error = __('Only requested action are accepted');
			throw new Exception($error);
		}
		if(!$class_name || !$property_name){
			$this->log("</Attribute.update ret=false reason='Invalid arguments'>", 'debug');
			$error = __("Invalid arguments");
			throw new Exception($error);
		}
		$dataSource = $this->Attribute->getDataSource();
		
		$dataSource->begin();
		
		try{
			//find previous attributes in the serie (objects with the same class_name, object_id, property_name)
			$last = false;
			if($dataset_id){
				//find previous attribute in the same dataset
				$last = $this->Attribute->find('first', array(
													   'conditions' => array(
															'Attribute.att_class_name' => $class_name,
															'Attribute.att_dataset_id' => $dataset_id,
															'Attribute.att_property_name' => $property_name
														)
													)
											);
			}else{
				//Dataset ids are Globally Unique
				//TODO: find a stronger UUID generation mechanism
				$dataset_id = String::uuid();
				//find previous attribute in the same object
				/*if($object_id){
					$last = $this->Attribute->find('first', array(
															   'conditions' => array(
																	'Attribute.att_class_name' => $class_name,
																	'Attribute.att_property_name' => $property_name,
																	'Attribute.att_object_id' => $object_id
																)
															)
												);
				}*/
			}
			if(!$last){
				//find previous attribute in any dataset
				$last = $this->Attribute->find('first', array(
													   'conditions' => array(
															'Attribute.att_class_name' => $class_name,
															'Attribute.att_property_name' => $property_name
														),
													   'order' => array('Attribute.att_created' => 'DESC')
													)
											);
			}
			$date = new DateTime();
			$now = $date->getTimestamp();
				
			if($last){
				$data['id'] = $last['Attribute']['id'];
				$data['att_serie_num'] = $last['Attribute']['att_serie_num'] + 1;
				$data['att_updated'] = $now;
			}else{
				$this->Attribute->create();
				
				$data['att_created'] = $now;
				$data['att_serie_num'] = 1;
			}
			$data['att_class_name'] = $class_name;
			$data['att_object_id'] = $object_id;
			$data['att_property_name'] = $property_name;
			$data['att_content'] = $content;
		
			$data['att_dataset_id'] = $dataset_id;

			if(!$this->Attribute->save($data)){
				$error = __('Unable to save attribute data');
				throw new Exception($error);
			}
			
			
			$dataSource->commit();
		}catch(Exception $e){
			$dataSource->rollback();
			$this->log($e->getMessage());
			if(!$error)
				$error = __(AppModel::$DEFAULT_ERROR_MESSAGE);
			throw new Exception($error);
		}
		$this->log("</Attribute.update ret=${dataset_id}>", 'debug');
		return $dataset_id;
	}
	/**
	 *Add all the columns of a record of a given object to a dataset of Attributes.
	 *If no dataset is specified, a new dataset will be created.
	 *@param $model, the class name of the model ('User', 'Project', etc).
	 *@param $object_id, the Object Id
	 *@param $dataset_id, the id of the Dataset, if empty a new Dataset id will be created
	 *@returns the id of the Dataset
	 *@throws Exception if arguments are invalid
	 */
	public function import($model = '', $object_id = 0, $dataset_id = ''){
		$this->log("<Attribute.import class_name=${model} object_id=${object_id} dataset_id=${dataset_id}>", 'debug');
		$error = '';
		if(!$model || !$object_id){
			$error= __('Invalid arguments');
			throw new Exception($error);
		}
		
		$db = $this->Attribute->getDataSource();
		$db->begin();
		
		try{
			if(!$dataset_id)
				$dataset_id = String::uuid();
				
			$this->log("dataset id:=${dataset_id}", 'debug');
			//load the object's data from database
			$this->loadModel($model);
			$this->{$model}->id = $object_id;
			$this->{$model}->read();
			$model_fields = $this->{$model}->data[$this->{$model}->alias];
			
			$this->log("model_fields:=". var_export($model_fields, true), 'debug');
			//foreach column of the object, create a new Attribute
			//[only if a corresponding attribute definition exists]
			$this->loadModel('AttributeDefinition');
			foreach($model_fields as $field_name => $field_value){
				$this->log("processing field:" . $field_name, 'debug');
				//look for a corresponding AttributeDefinition object
				$this->log("checking if definition exists for field:" . $field_name, 'debug');
				$atd = $this->AttributeDefinition->findByAtdPropertyName($field_name);
				if(!$atd)
					continue;
				$this->log("definition exists for field:" . $field_name, 'debug');
				/*$options = array(
					'att_class_name' => $model, 
					'att_object_id' => $object_id, 
					'att_property_name' => $field_name, 
					'att_content' => $field_value, 
					'att_dataset_id' => $dataset_id
				);*/
				$this->log("ceating new attribute for field:" . $field_name, 'debug');
				$ok = $this->update($model, $object_id, $field_name, $field_value, $dataset_id);
				$this->log("new attribute has been created for field:" . $field_name, 'debug');
				if(!$ok){
					$error = __("Failed to update attribute (%s) with value (%s)", $field_name, $field_value);
					throw new Exception($error);
				}					
			}
			//$this->_importDependents($model, $object_id, $dataset_id);
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			throw $e;
			/*if(!$error)
				$error = __(AppModel::$DEFAULT_ERROR_MESSAGE);
			throw new Exception($error);*/
		}
		$this->log("</Attribute.import>", 'debug');
		return $dataset_id;
	}
	
	private function _imporDependents($model = "", $object_id = 0, $dataset_id = ''){
		$this->log("<Attribute._imporDependents class_name=${model} object_id=${object_id}>", 'debug');
		$error = '';
		if(!$model || !$object_id){
			$error= __('Invalid arguments');
			throw new Exception($error);
		}
		
		try{
			if(!$dataset_id)
				$dataset_id = String::uuid();
			//load the object's data from database
			$this->loadModel($model);
			$this->{$model}->id = $object_id;
			$this->{$model}->read();
			
			$dependents= $this->{$model}->hasOne;
			foreach($dependents as $dependentAlias => $dependent){
				$dependentModel = $dependent['className'];
				$dependentId = $this->{$model}->data[$dependentAlias]['id'];
				if($dependentModel && $dependentId){
					$this->import($dependentModel, $dependentId, $dataset_id);
				}
			}
		}catch(Exception $e){
			$this->log($e->getMessage());
			if(!$error)
				$error = __(AppModel::$DEFAULT_ERROR_MESSAGE);
			throw new Exception($error);
		}
		$this->log("</Attribute._imporDependents>", 'debug');
	}
	
	/**
	 *Load all the Attributes of a specified Dataset from the database.
	 *@param $dataset_id, the id of the Dataset
	 *@returns HTML or an array of Attribute objects
	 */
	public function dataset($dataset_id){
		$this->log("<Attribute.dataset dataset_id=${dataset_id}>", 'debug');
		if(!$dataset_id){
			$this->log("</Attribute.dataset error='Invalid arguments'>", 'debug');
			return;
		}
		$attributes = $this->Attribute->find('all', array('conditions' => array('Attribute.att_dataset_id' => $dataset_id)));
		if(!empty($this->request->params['requested'])){
			$this->log("</Attribute.dataset>", 'debug');
			return $attributes;
		}
		return $this->redirect($this->referer());
	}
}
?>