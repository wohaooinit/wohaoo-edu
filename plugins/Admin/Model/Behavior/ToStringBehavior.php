<?php
class ToStringBehavior extends ModelBehavior {

    public function setup(Model $Model, $config = array()) {
        if(empty($Model->displayField) && !empty($Model->toString) && !is_array($Model->toString)) {
            $Model->displayField = $Model->toString;
        }
    }

    public function beforeFind(Model $model, $query) {
        if(!empty($query['list']) && is_array($model->toString)) {

            $query['recursive'] = 1;

            $numDisplayFieldParams = sizeof($model->toString);
            for($i = 1; $i < $numDisplayFieldParams; $i++) {
                $query['fields'][] = $model->toString[$i];
            }

            //change valuePath
            $display = $model->toString;
            for($i = 1; $i < $numDisplayFieldParams; $i++) {
                $display[$i] = "{n}." . $display[$i];
            }
            $query['list']['valuePath'] = $display;
        }
        if(!empty($query['order'])) {
            foreach($query['order'] as $key => $value) {
                if(is_array($value)) {
                    foreach($value as $valueKey => $valueOption) {
                        if(strpos($valueOption, '%') !== false) {
                            unset($query['order'][$key][$valueKey]);
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function afterFind(Model $model, $results, $primary) {
        $numResults = sizeof($results);
        if($primary) {

            $results = $this->createToStrings($results, $model->alias, (!empty($model->toString)) ? $model->toString : $model->displayField);
            if (!empty($model->belongsTo)) {
                foreach ($model->belongsTo as $alias => $details) {
                    $results = $this->createToStrings($results, $alias, (!empty($model->$alias->toString)) ? $model->$alias->toString : $model->$alias->displayField);
                }
            }
        } else {
            $toString = (!empty($model->toString)) ? $model->toString : $model->displayField;
            for($i = 0; $i < $numResults; $i++) {
                if(is_array($model->displayField)) {
                    $results[$i]['toString'] = 'toString';
                } else {
                    $results[$i]['toString'] = $results[$i][$toString];
                }
            }
        }

        return $results;
    }

    private function createToStrings($results, $alias, $displayField) {
        $numResults = sizeof($results);
        $toStrings = array();
        if(is_array($displayField)) {
            $display = $displayField;
            $format = array_shift($display);
            $numParams = sizeof($display);
            for($i = 0; $i < $numParams; $i++) {
                $display[$i] = "{n}." . $display[$i];
            }
            $toStrings = Hash::format($results, $display, $format);
        } else {
            for($i = 0; $i < $numResults; $i++) {
                if(!empty($results[$i][$alias]) && !empty($results[$i][$alias][$displayField])) {
                    $toStrings[] = $results[$i][$alias][$displayField];
                } else {
                    $toStrings[] = '';
                }
            }
        }
        $numToStrings = sizeof($toStrings);
        for($i = 0; $i < $numResults; $i++) {
            if(!empty($results[$i][$alias])) {
                $results[$i][$alias]['toString'] = !empty($toStrings[$i]) ? $toStrings[$i] : '';
            }
        }
        return $results;
    }
}