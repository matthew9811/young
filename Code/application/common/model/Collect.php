<?php


namespace app\common\model;


use think\Model;

class Collect extends Model
{
    protected $id = "id";
    protected $articleId = "article_id";
    protected $customerId = "customer_id";

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param string $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

}