<?php
namespace Chobit\Entity;

class Blog {
    public $db;
    public function __construct($db)
    {
        $this->db = $db;

    }
    public function store($params)
    {
        $article = $this->db->dispense('article');
        $article->title = $params['title'];
        $article->tag = $params['tag'];
        $article->article = $params['article'];
        $id = $this->db->store($article);
        return $id;
    }
}
