<?php

namespace Forexceccom\Models;

class ForexcecGlossary extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $glossary_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $glossary_icon;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_meta_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_meta_description;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $glossary_meta_image;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $glossary_summary;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $glossary_content;

    /**
     *
     * @var string
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $glossary_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $glossary_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $glossary_is_home;

    /**
     *
     * @var string
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $glossary_insert_time;

    /**
     *
     * @var string
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $glossary_update_time;

    /**
     * Method to set the value of field glossary_id
     *
     * @param int $glossary_id
     * @return $this
     */
    public function setGlossaryId($glossary_id)
    {
        $this->glossary_id = $glossary_id;
        return $this;
    }

    /**
     * Method to set the value of field glossary_name
     *
     * @param string $glossary_name
     * @return $this
     */
    public function setGlossaryName($glossary_name)
    {
        $this->glossary_name = $glossary_name;
        return $this;
    }

    /**
     * Method to set the value of field glossary_icon
     *
     * @param string $glossary_icon
     * @return $this
     */
    public function setGlossaryIcon($glossary_icon)
    {
        $this->glossary_icon = $glossary_icon;
        return $this;
    }

    /**
     * Method to set the value of field glossary_keyword
     *
     * @param string $glossary_keyword
     * @return $this
     */
    public function setGlossaryKeyword($glossary_keyword)
    {
        $this->glossary_keyword = $glossary_keyword;
        return $this;
    }

    /**
     * Method to set the value of field glossary_title
     *
     * @param string $glossary_title
     * @return $this
     */
    public function setGlossaryTitle($glossary_title)
    {
        $this->glossary_title = $glossary_title;
        return $this;
    }

    /**
     * Method to set the value of field glossary_meta_keyword
     *
     * @param string $glossary_meta_keyword
     * @return $this
     */
    public function setGlossaryMetaKeyword($glossary_meta_keyword)
    {
        $this->glossary_meta_keyword = $glossary_meta_keyword;
        return $this;
    }

    /**
     * Method to set the value of field glossary_meta_description
     *
     * @param string $glossary_meta_description
     * @return $this
     */
    public function setGlossaryMetaDescription($glossary_meta_description)
    {
        $this->glossary_meta_description = $glossary_meta_description;
        return $this;
    }

    /**
     * Method to set the value of field glossary_meta_image
     *
     * @param string $glossary_meta_image
     * @return $this
     */
    public function setGlossaryMetaImage($glossary_meta_image)
    {
        $this->glossary_meta_image = $glossary_meta_image;
        return $this;
    }

    /**
     * Method to set the value of field glossary_summary
     *
     * @param string $glossary_summary
     * @return $this
     */
    public function setGlossarySummary($glossary_summary)
    {
        $this->glossary_summary = $glossary_summary;
        return $this;
    }

    /**
     * Method to set the value of field glossary_content
     *
     * @param string $glossary_content
     * @return $this
     */
    public function setGlossaryContent($glossary_content)
    {
        $this->glossary_content = $glossary_content;
        return $this;
    }

    /**
     * Method to set the value of field glossary_order
     *
     * @param string $glossary_order
     * @return $this
     */
    public function setGlossaryOrder($glossary_order)
    {
        $this->glossary_order = $glossary_order;
        return $this;
    }

    /**
     * Method to set the value of field glossary_active
     *
     * @param string $glossary_active
     * @return $this
     */
    public function setGlossaryActive($glossary_active)
    {
        $this->glossary_active = $glossary_active;
        return $this;
    }

    /**
     * Method to set the value of field glossary_is_home
     *
     * @param string $glossary_is_home
     * @return $this
     */
    public function setGlossaryIsHome($glossary_is_home)
    {
        $this->glossary_is_home = $glossary_is_home;
        return $this;
    }

    /**
     * Method to set the value of field glossary_insert_time
     *
     * @param string $glossary_insert_time
     * @return $this
     */
    public function setGlossaryInsertTime($glossary_insert_time)
    {
        $this->glossary_insert_time = $glossary_insert_time;
        return $this;
    }

    /**
     * Method to set the value of field glossary_update_time
     *
     * @param string $glossary_update_time
     * @return $this
     */
    public function setGlossaryUpdateTime($glossary_update_time)
    {
        $this->glossary_update_time = $glossary_update_time;
        return $this;
    }

    /**
     * Returns the value of field glossary_id
     *
     * @return int
     */
    public function getGlossaryId()
    {
        return $this->glossary_id;
    }

    /**
     * Returns the value of field glossary_name
     *
     * @return string
     */
    public function getGlossaryName()
    {
        return $this->glossary_name;
    }

    /**
     * Returns the value of field glossary_icon
     *
     * @return string
     */
    public function getGlossaryIcon()
    {
        return $this->glossary_icon;
    }

    /**
     * Returns the value of field glossary_keyword
     *
     * @return string
     */
    public function getGlossaryKeyword()
    {
        return $this->glossary_keyword;
    }

    /**
     * Returns the value of field glossary_title
     *
     * @return string
     */
    public function getGlossaryTitle()
    {
        return $this->glossary_title;
    }

    /**
     * Returns the value of field glossary_meta_keyword
     *
     * @return string
     */
    public function getGlossaryMetaKeyword()
    {
        return $this->glossary_meta_keyword;
    }

    /**
     * Returns the value of field glossary_meta_description
     *
     * @return string
     */
    public function getGlossaryMetaDescription()
    {
        return $this->glossary_meta_description;
    }

    /**
     * Returns the value of field glossary_meta_image
     *
     * @return string
     */
    public function getGlossaryMetaImage()
    {
        return $this->glossary_meta_image;
    }

    /**
     * Returns the value of field glossary_summary
     *
     * @return string
     */
    public function getGlossarySummary()
    {
        return $this->glossary_summary;
    }

    /**
     * Returns the value of field glossary_content
     *
     * @return string
     */
    public function getGlossaryContent()
    {
        return $this->glossary_content;
    }

    /**
     * Returns the value of field glossary_order
     *
     * @return string
     */
    public function getGlossaryOrder()
    {
        return $this->glossary_order;
    }

    /**
     * Returns the value of field glossary_active
     *
     * @return string
     */
    public function getGlossaryActive()
    {
        return $this->glossary_active;
    }

    /**
     * Returns the value of field glossary_is_home
     *
     * @return string
     */
    public function getGlossaryIsHome()
    {
        return $this->glossary_is_home;
    }

    /**
     * Returns the value of field glossary_insert_time
     *
     * @return string
     */
    public function getGlossaryInsertTime()
    {
        return $this->glossary_insert_time;
    }

    /**
     * Returns the value of field glossary_update_time
     *
     * @return string
     */
    public function getGlossaryUpdateTime()
    {
        return $this->glossary_update_time;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_glossary';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecGlossary[]|ForexcecGlossary
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecGlossary
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecGlossary
     */
    public static function findFirstById($id)
    {
        return ForexcecGlossary::findFirst(array(
            "glossary_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }

}