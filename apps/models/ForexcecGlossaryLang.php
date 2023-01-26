<?php

namespace Forexceccom\Models;

class ForexcecGlossaryLang extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=5, nullable=false)
     */
    protected $glossary_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $glossary_name;

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
     * Method to set the value of field glossary_lang_code
     *
     * @param string $glossary_lang_code
     * @return $this
     */
    public function setGlossaryLangCode($glossary_lang_code)
    {
        $this->glossary_lang_code = $glossary_lang_code;
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
     * Returns the value of field glossary_id
     *
     * @return int
     */
    public function getGlossaryId()
    {
        return $this->glossary_id;
    }

    /**
     * Returns the value of field glossary_lang_code
     *
     * @return string
     */
    public function getGlossaryLangCode()
    {
        return $this->glossary_lang_code;
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
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_glossary_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecGlossaryLang[]|ForexcecGlossaryLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecGlossaryLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    /**
     *
     * @param integer $glossary_id
     * @return ForexcecGlossaryLang[]|ForexcecGlossaryLang
     */
    public static function findById($glossary_id)
    {
        return ForexcecGlossaryLang::find(array(
            "glossary_id =:ID:",
            'bind' => array('ID' => $glossary_id)
        ));
    }

}