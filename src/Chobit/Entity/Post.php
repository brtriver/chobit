<?php
namespace Chobit\Entity;

use R;

class Post {
    const POST = 'post';
    public function store($params)
    {
        $post = R::dispense(self::POST);
        $post->title = $params['title'];
        $post->content = $params['content'];
        $id = R::store($post);
        return $id;
    }
    public function findAll()
    {
        $posts = R::find(self::POST);
        return $posts;
    }
    public function findById($id)
    {
        $post = R::load(self::POST, $id);
        return $post->export();
    }
    public function update($id, $params) {
        if (!$this->isExist($id)) {
            return false;
        }
        $post = R::load(self::POST, $id);
        $post->title = $params['title'];
        $post->content = $params['content'];
        $id = R::store($post);
        return $id;
    }
    public function isExist($id) {
        $post = R::load(self::POST, $id);
        return ($post->id)? true: false;
    }
    public function delete($id) {
        if (!$this->isExist($id)) {
            return false;
        }
        $post = R::load(self::POST, $id);
        $id = R::trash($post);
        return $id;
    }
}
