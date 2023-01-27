<?php

namespace Score\Models;

class ScMatch extends \Phalcon\Mvc\Model
{
    protected $match_id;
    protected $match_name;
    protected $match_status;
    protected $match_home_id;
    protected $match_away_id;
    protected $match_home_score;
    protected $match_away_score;
    protected $match_insert_time;
    protected $match_time;
    protected $match_start_time;
    protected $match_order;


    /**
     * @return mixed
     */
    public function getMatchId()
    {
        return $this->Match_id;
    }

    /**
     * @param mixed $Match_id
     */
    public function setMatchId($Match_id)
    {
        $this->Match_id = $Match_id;
    }

    /**
     * @return mixed
     */
    public function getMatchName()
    {
        return $this->match_name;
    }

    /**
     * @param mixed $match_name
     */
    public function setMatchName($match_name)
    {
        $this->match_name = $match_name;
    }

    /**
     * @return mixed
     */
    public function getMatchStatus()
    {
        return $this->match_status;
    }

    /**
     * @param mixed $match_status
     */
    public function setMatchStatus($match_status)
    {
        $this->match_status = $match_status;
    }

    /**
     * @return mixed
     */
    public function getMatchHomeId()
    {
        return $this->match_home_id;
    }

    /**
     * @param mixed $match_home_id
     */
    public function setMatchHomeId($match_home_id)
    {
        $this->match_home_id = $match_home_id;
    }

    /**
     * @return mixed
     */
    public function getMatchAwayId()
    {
        return $this->match_away_id;
    }

    /**
     * @param mixed $match_away_id
     */
    public function setMatchAwayId($match_away_id)
    {
        $this->match_away_id = $match_away_id;
    }

    /**
     * @return mixed
     */
    public function getMatchHomeScore()
    {
        return $this->match_home_score;
    }

    /**
     * @param mixed $match_home_score
     */
    public function setMatchHomeScore($match_home_score)
    {
        $this->match_home_score = $match_home_score;
    }

    /**
     * @return mixed
     */
    public function getMatchAwayScore()
    {
        return $this->match_away_score;
    }

    /**
     * @param mixed $match_away_score
     */
    public function setMatchAwayScore($match_away_score)
    {
        $this->match_away_score = $match_away_score;
    }

    /**
     * @return mixed
     */
    public function getMatchInsertTime()
    {
        return $this->match_insert_time;
    }

    /**
     * @param mixed $match_insert_time
     */
    public function setMatchInsertTime($match_insert_time)
    {
        $this->match_insert_time = $match_insert_time;
    }

    /**
     * @return mixed
     */
    public function getMatchTime()
    {
        return $this->match_time;
    }

    /**
     * @param mixed $match_time
     */
    public function setMatchTime($match_time)
    {
        $this->match_time = $match_time;
    }

    /**
     * @return mixed
     */
    public function getMatchStartTime()
    {
        return $this->match_start_time;
    }

    /**
     * @param mixed $match_start_time
     */
    public function setMatchStartTime($match_start_time)
    {
        $this->match_start_time = $match_start_time;
    }

    /**
     * @return mixed
     */
    public function getMatchOrder()
    {
        return $this->match_order;
    }


    /**
     * @param mixed $match_order
     */
    public function setMatchOrder($match_order)
    {
        $this->match_order = $match_order;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatch[]|ScMatch
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatch
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

 
}