<?php


namespace app\common\model;


use think\Model;

class Article extends Model
{
    protected $table = "article";
    protected $id = 'id';
    protected $title = 'title';
    protected $customerId = 'customer_id';
    protected $content = 'content';
    protected $issuingTime = 'issuing_time';
    protected $reviewStatus = 'review_status';
    protected $reviewer = 'reviewer';
    protected $cover = 'cover';

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getIssuingTime()
    {
        return $this->issuingTime;
    }

    /**
     * @param string $issuingTime
     */
    public function setIssuingTime($issuingTime)
    {
        $this->issuingTime = $issuingTime;
    }

    /**
     * @return string
     */
    public function getReviewStatus()
    {
        return $this->reviewStatus;
    }

    /**
     * @param string $reviewStatus
     */
    public function setReviewStatus($reviewStatus)
    {
        $this->reviewStatus = $reviewStatus;
    }

    /**
     * @return string
     */
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * @param string $reviewer
     */
    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param string $cover
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
    }



}