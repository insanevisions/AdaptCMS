<?php

class ForumPostsController extends AdaptbbAppController
{
    /**
    * Name of the Controller, 'ForumPosts'
    */
	public $name = 'ForumPosts';

    /**
    * array of permissions for this page
    */
	private $permissions;

    /**
    * In this beforeFilter we will get the permissions to be used in the view files
    */
	public function beforeFilter()
	{
		parent::beforeFilter();
	
		$this->permissions = $this->getPermissions();
	}

    /**
    * Attemps to create a post record
    *
    * @return mixed
    */
    public function ajax_post()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        if ($html_tags = Configure::read('Adaptbb.html_tags_allowed'))
        {
            $this->request->data['ForumPost']['content'] = strip_tags(
                $this->request->data['ForumPost']['content'],
                $html_tags . ',<blockquote>,<small>'
            );
        }

        if ($user_id = $this->Auth->user('id'))
        {
            $this->request->data['ForumPost']['user_id'] = $user_id;
        }

        $forum_id = $this->request->data['ForumPost']['forum_id'];
        $topic_id = $this->request->data['ForumPost']['topic_id'];

        if (!empty($this->request->data))
        {
            $replies_num = $this->ForumPost->ForumTopic->findById($topic_id);

            if ($replies_num['ForumTopic']['status'] == 0)
            {
                return json_encode(array(
                    'status' => false,
                    'message' => 'This topic is closed'
                ));
            }

            $data = array();

            $data['ForumTopic']['id'] = $topic_id;
            $data['ForumTopic']['num_posts'] = $replies_num['ForumTopic']['num_posts'] + 1;

            $this->ForumPost->ForumTopic->save($data);

            $this->ForumPost->create();

            if ($this->ForumPost->save($this->request->data))
            {
                $posts_num = $this->ForumPost->ForumTopic->Forum->findById($forum_id);

                $data = array();

                $data['Forum']['id'] = $forum_id;
                $data['Forum']['num_posts'] = $posts_num['Forum']['num_posts'] + 1;

                $this->ForumPost->ForumTopic->Forum->save($data);

                return json_encode(array(
                    'status' => true,
                    'message' => 'Your post has been made'
                ));
            } else {
                return json_encode(array(
                    'status' => false,
                    'message' => 'Your post could not be made'
                ));
            }
        }
    }

    public function ajax_edit()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $return = array(
            'status' => true,
            'message' => 'The post has been updated.'
        );

        $id = $this->request->data['ForumPost']['id'];

        $post = $this->ForumPost->find('first', array(
            'conditions' => array(
                'ForumPost.id' => $id
            ),
            'contain' => array(
                'User',
                'ForumTopic'
            )
        ));

        if ($this->request->data['User']['id'] != $this->Auth->user('id') && $this->permissions['any'] == 0)
        {
            $return = array(
                'status' => false,
                'message' => 'You do not have access to edit other users posts.'
            );
        }

        if (empty($post['ForumPost']))
        {
            $return = array(
                'status' => false,
                'message' => 'Invalid post specified.'
            );
        }

        if (!empty($return['status']) && !empty($this->request->data))
        {
            $this->ForumPost->id = $id;

            if ($html_tags = Configure::read('Adaptbb.html_tags_allowed'))
            {
                $this->request->data['ForumPost']['content'] = strip_tags(
                    $this->request->data['ForumPost']['content'],
                    $html_tags . ',<blockquote>,<small>'
                );
            }

            if (!$this->ForumPost->save($this->request->data))
            {
                $return = array(
                    'status' => false,
                    'message' => 'Your post could not be updated.'
                );
            }
        }

        return json_encode($return);
    }

    public function delete($id)
    {
        $post = $this->ForumPost->find('first', array(
            'conditions' => array(
                'ForumPost.id' => $id
            ),
            'contain' => array(
                'User',
                'ForumTopic' => array(
                    'Forum'
                )
            )
        ));

        if (empty($post['ForumPost']))
        {
            $this->Session->setFlash('Post could not be found.', 'flash_error');
            $this->redirect(array(
                'controller' => 'forum_topics',
                'action' => 'view', 
                $this->slug($post['ForumTopic']['subject']) 
            ));
        }

        if ($post['User']['id'] != $this->Auth->user('id') && $this->permissions['any'] == 0)
        {
            $this->Session->setFlash('You cannot access another users item.', 'flash_error');
            $this->redirect(array(
                'controller' => 'forum_topics',
                'action' => 'view', 
                $this->slug($post['ForumTopic']['subject']) 
            ));               
        }

        $this->ForumPost->id = $id;

        if ($this->ForumPost->saveField('deleted_time', $this->ForumPost->dateTime()) )
        {
            $this->ForumPost->ForumTopic->Forum->id = $post['ForumTopic']['Forum']['id'];

            $data = array();
            $data['Forum']['id'] = $post['ForumTopic']['Forum']['id'];
            $data['Forum']['num_posts'] = $post['ForumTopic']['Forum']['num_posts'] - 1;

            $this->ForumPost->ForumTopic->Forum->save($data);

            $this->ForumPost->ForumTopic->id = $post['ForumTopic']['id'];

            $data = array();
            $data['ForumTopic']['id'] = $post['ForumTopic']['id'];
            $data['ForumTopic']['num_posts'] = $post['ForumTopic']['num_posts'] - 1;

            $this->ForumPost->ForumTopic->save($data);

            $this->Session->setFlash('The post has been deleted.', 'flash_success');
            $this->redirect(array(
                'controller' => 'forum_topics',
                'action' => 'view', 
                $this->slug($post['ForumTopic']['subject']) 
            )); 
        } else {
            $this->Session->setFlash('Unable to delete the post.', 'flash_error');
        }
    }
}