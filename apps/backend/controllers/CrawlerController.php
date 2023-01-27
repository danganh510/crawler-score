<?php

namespace Score\Backend\Controllers;

use Score\Models\ForexcecConfig;
use Score\Models\ForexcecLanguage;
use Score\Models\ScTeam;
use Score\Repositories\Config;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Repositories\Activity;
use Score\Repositories\Language;
use Score\Repositories\CrawlerScore;
use Score\Repositories\Team;
use Goutte\Client;
use Score\Models\ScMatch;

class CrawlerController extends ControllerBase
{

    public function indexAction()
    {
        $start_time_cron = time();
        echo "Start crawl data in ".$this->my->formatDateTime(time()) ."/n/r";
        $link =  'https://www.livescores.com';
        $param_live = "/football/{$this->my->formatDateYMD($start_time_cron)}/?tz=7";
        $url = $link.$param_live;

        $client = new client();
        $crawler = $client->request('GET', $url);
        $list_match = CrawlerScore::Crawl($crawler);
       
        foreach ($list_match as $match) {
            $home = Team::findByName($match['home']);
         
            if (!$home) {
                $home = new ScTeam();
                
                $home->setTeamName($match['home']);
                $home->setTeamActive("Y");
                $home->save();
                
            }
            $away = Team::findByName($match['away']);
            if (!$away) {
                $away = new ScTeam();
                $away->setTeamName($match['away']);
                $away->setTeamActive("Y");
                $away->save();
            }
            $matchSave = ScMatch::findFirst([
                "match_home_id = :home_id: AND match_away_id = :away_id: AND match_status != 'F'",
                'bind' => [
                    'home_id' => $home->getTeamId(),
                    'away_id' => $away->getTeamId(),
                ]
            ]);
            if (!$matchSave) {
                $matchSave = new ScMatch();
                $matchSave->setMatchName($match['home'] ." - ". $match['away']);
                $matchSave->setMatchHomeId($home->getTeamId());
                $matchSave->setMatchAwayId($away->getTeamId());
                $matchSave->setMatchInsertTime(time());
                if (strpos($match['time'],"'") ) {
                    $time = str_replace("'", "", $match['time']);
                    $start_time = time() - $time * 60;
                } elseif ($match['time'] == "FT" ) {
                    $time = 45;
                    $start_time = time() - $time * 60;
                } elseif ($match['time'] == "HT" || $match['time'] == "AET") {
                    $time = 90;
                    $start_time = time() - $time * 60;
                } else {
                   
                    $start_time = $this->my->formatDateTimeSendEmail(time()) . " " . $match['time'];
                   
                    $start_time = strtotime($start_time);
                   
                }
                $matchSave->setMatchStartTime($start_time);
          
            }
            if (strpos($match['time'],"'")) {
                $time = str_replace("'", "", $match['time']);
                $matchSave->setMatchStatus("S");
              
            } else {
                $time = 0;
                $matchSave->setMatchStatus("W");
            }
            $matchSave->setMatchTime($time);
            $matchSave->setMatchHomeScore($match['home_score']);
            $matchSave->setMatchAwayScore($match['away_score']);
            
  
            $matchSave->setMatchOrder(1);

            $matchSave->save();
          
        }
        die();
    }
    public function detailAction()
    {
        echo "Start crawl data in ".$this->my->formatDateTime(time()) ."/n/r";
        $link =  'https://www.livescores.com';
        $param_live = "/football/germany/bundesliga/mainz-vs-borussia-dortmund/704822/?tz=7&tab=tracker";
        $url = $link.$param_live;

        $client = new client();
        $crawler = $client->request('GET', $url);
        $list_match = CrawlerScore::CrawlDetailTracker($crawler);
        
        die();
    }
}