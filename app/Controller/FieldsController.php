<?php

class FieldsController extends AppController {
	public $name = 'Fields';

	public function admin_index()
	{
        if (!isset($this->params->named['trash'])) {
            $this->paginate = array(
                'group' => 'title',
                'order' => 'Field.created DESC',
                'limit' => $this->pageLimit,
                'conditions' => array(
                    'Field.deleted_time' => '0000-00-00 00:00:00'
                )
            );
        } else {
            $this->paginate = array(
                'group' => 'title',
                'order' => 'Field.created DESC',
                'limit' => $this->pageLimit,
                'conditions' => array(
                    'Field.deleted_time' => '0000-00-00 00:00:00'
                )
            );
        }
        
        $this->request->data = $this->paginate('Field');
	}

	public function admin_add()
	{
		$this->set('categories', $this->Field->Category->find('list', array(
            'conditions' => array(
                'Category.deleted_time' => '0000-00-00 00:00:00'
            )
        )));

        if ($this->request->is('post')) {
        	if (!empty($this->request->data['Field']['category_id'][0])) {
        		foreach ($this->request->data['Field']['category_id'] as $category_id) {
        			$this->Field->create();

        			$this->request->data['Field']['category_id'] = $category_id;

                                if ($this->request->data['Field']['required'] == 1) {
                                        $rules[] = "required: true,";
                                } else {
                                        $rules[] = "required: false,";
                                }
                                if ($this->request->data['Field']['field_limit_min'] > 0) {
                                        $rules[] = "minlength: ".$this->request->data['Field']['field_limit_min'].",";
                                }
                                if ($this->request->data['Field']['field_limit_max'] > 0) {
                                        $rules[] = "maxlength: ".$this->request->data['Field']['field_limit_max'].",";
                                }
                                if ($this->request->data['Field']['field_type'] == "email") {
                                        $rules[] = "email: true,";
                                }
                                if ($this->request->data['Field']['field_type'] == "url") {
                                        $rules[] = "url: true,";
                                }
                                if ($this->request->data['Field']['field_type'] == "num") {
                                        $rules[] = "number: true,";
                                }

                                $this->request->data['Field']['rules'] = json_encode($rules);
                                unset($rules);

                                if (empty($this->request->data['Field']['label'])) {
                                        $this->request->data['Field']['label'] = $this->request->data['Field']['title'];
                                }
        			$this->request->data['Field']['title'] = $this->slug($this->request->data['Field']['title']);
                    if (!empty($this->request->data['FieldData'])) {
                        $this->request->data['Field']['field_options'] = 
                            json_encode($this->request->data['FieldData']);
                    }

        			$this->Field->save($this->request->data);
        			
        			$save = true;
        		}
        	} else {
        		$this->Field->create();

                        if ($this->request->data['Field']['required'] == 1) {
                                $rules[] = "required: true,";
                        } else {
                                $rules[] = "required: false,";
                        }
                        if ($this->request->data['Field']['field_limit_min'] > 0) {
                                $rules[] = "minlength: ".$this->request->data['Field']['field_limit_min'].",";
                        }
                        if ($this->request->data['Field']['field_limit_max'] > 0) {
                                $rules[] = "maxlength: ".$this->request->data['Field']['field_limit_max'].",";
                        }
                        if ($this->request->data['Field']['field_type'] == "email") {
                                $rules[] = "email: true,";
                        }
                        if ($this->request->data['Field']['field_type'] == "url") {
                                $rules[] = "url: true,";
                        }
                        if ($this->request->data['Field']['field_type'] == "num") {
                                $rules[] = "number: true,";
                        }

                        $this->request->data['Field']['rules'] = json_encode($rules);
                        unset($rules);

                        if (empty($this->request->data['Field']['label'])) {
                                $this->request->data['Field']['label'] = $this->request->data['Field']['title'];
                        }
        		$this->request->data['Field']['title'] = $this->slug($this->request->data['Field']['title']);
                if (!empty($this->request->data['FieldData'])) {
                    $this->request->data['Field']['field_options'] = 
                        json_encode($this->request->data['FieldData']);
                }

        		$this->Field->save($this->request->data);

        		$save = true;
        	}

            if (isset($save)) {
                $this->Session->setFlash(Configure::read('alert_btn').'<strong>Success</strong> Your field has been added.', 'default', array('class' => 'alert alert-success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(Configure::read('alert_btn').'<strong>Error</strong> Unable to add your field.', 'default', array('class' => 'alert alert-error'));
            }
        } 
	}

	public function admin_edit($id = null)
	{
        $this->set('categories', $this->Field->Category->find('list', array(
            'conditions' => array(
                'Category.deleted_time' => '0000-00-00 00:00:00'
            )
        )));

	    if ($this->request->is('get')) {
	        $this->request->data = $this->Field->findById($id);
	    } else {

        	if (!empty($this->request->data['Field']['category_id'][0])) {
        		foreach ($this->request->data['Field']['category_id'] as $category_id) {
        			$this->request->data['Field']['category_id'] = $category_id;

                                if ($this->request->data['Field']['required'] == 1) {
                                        $rules[] = "required: true,";
                                } else {
                                        $rules[] = "required: false,";
                                }
                                if ($this->request->data['Field']['field_limit_min'] > 0) {
                                        $rules[] = "minlength: ".$this->request->data['Field']['field_limit_min'].",";
                                }
                                if ($this->request->data['Field']['field_limit_max'] > 0) {
                                        $rules[] = "maxlength: ".$this->request->data['Field']['field_limit_max'].",";
                                }
                                if ($this->request->data['Field']['field_type'] == "email") {
                                        $rules[] = "email: true,";
                                }
                                if ($this->request->data['Field']['field_type'] == "url") {
                                        $rules[] = "url: true,";
                                }
                                if ($this->request->data['Field']['field_type'] == "num") {
                                        $rules[] = "number: true,";
                                }

                                $this->request->data['Field']['rules'] = json_encode($rules);
                                unset($rules);

                                if (empty($this->request->data['Field']['label'])) {
                                        $this->request->data['Field']['label'] = $this->request->data['Field']['title'];
                                }
        			$this->request->data['Field']['title'] = $this->slug($this->request->data['Field']['title']);
                    if (!empty($this->request->data['FieldData'])) {
                        $this->request->data['Field']['field_options'] = 
                            json_encode($this->request->data['FieldData']);
                    }

        			$this->Field->save($this->request->data);
        			
        			$save = true;
        		}
        	} else {
                        if ($this->request->data['Field']['required'] == 1) {
                                $rules[] = "required: true,";
                        } else {
                                $rules[] = "required: false,";
                        }
                        if ($this->request->data['Field']['field_limit_min'] > 0) {
                                $rules[] = "minlength: ".$this->request->data['Field']['field_limit_min'].",";
                        }
                        if ($this->request->data['Field']['field_limit_max'] > 0) {
                                $rules[] = "maxlength: ".$this->request->data['Field']['field_limit_max'].",";
                        }
                        if ($this->request->data['Field']['field_type'] == "email") {
                                $rules[] = "email: true,";
                        }
                        if ($this->request->data['Field']['field_type'] == "url") {
                                $rules[] = "url: true,";
                        }
                        if ($this->request->data['Field']['field_type'] == "num") {
                                $rules[] = "number: true,";
                        }

                        $this->request->data['Field']['rules'] = json_encode($rules);
                        unset($rules);
                        
                        if (empty($this->request->data['Field']['label'])) {
                                $this->request->data['Field']['label'] = $this->request->data['Field']['title'];
                        }
        		$this->request->data['Field']['title'] = $this->slug($this->request->data['Field']['title']);
                if (!empty($this->request->data['FieldData'])) {
                    $this->request->data['Field']['field_options'] = 
                        json_encode($this->request->data['FieldData']);
                }

                $this->Field->save($this->request->data);

        		$save = true;
        	}

	        if (isset($save)) {
	            $this->Session->setFlash(Configure::read('alert_btn').'<strong>Success</strong> Your field has been updated.', 'default', array('class' => 'alert alert-success'));
	            $this->redirect(array('action' => 'index'));
	        } else {
	            $this->Session->setFlash(Configure::read('alert_btn').'<strong>Error</strong> Unable to update your field.', 'default', array('class' => 'alert alert-error'));
	        }
	    }

	}

	public function admin_delete($id = null, $title = null, $permanent = null)
	{
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->Field->id = $id;

        if (!empty($permanent)) {
            $delete = $this->Field->delete($id);
        } else {
            $delete = $this->Field->saveField('deleted_time', $this->Field->dateTime());
        }

        if ($delete) {
	        $this->Session->setFlash(Configure::read('alert_btn').'<strong>Success</strong> The field `'.$title.'` has been deleted.', 'default', array('class' => 'alert alert-success'));
	        $this->redirect(array('action' => 'index'));
	    } else {
	    	$this->Session->setFlash(Configure::read('alert_btn').'<strong>Error</strong> The field `'.$title.'` has NOT been deleted.', 'default', array('class' => 'alert alert-error'));
	        $this->redirect(array('action' => 'index'));
	    }
	}

    public function admin_restore($id = null, $title = null)
    {
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->Field->id = $id;

        if ($this->Field->saveField('deleted_time', '0000-00-00 00:00:00')) {
            $this->Session->setFlash(Configure::read('alert_btn').'<strong>Success</strong> The field `'.$title.'` has been restored.', 'default', array('class' => 'alert alert-success'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(Configure::read('alert_btn').'<strong>Error</strong> The field `'.$title.'` has NOT been restored.', 'default', array('class' => 'alert alert-error'));
            $this->redirect(array('action' => 'index'));
        }
    }
}