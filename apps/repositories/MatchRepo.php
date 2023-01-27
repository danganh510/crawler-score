<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScMatch;
use Symfony\Component\DomCrawler\Crawler;

class MatchRepo extends Component
{
    public  function saveMatch($match,$home,$away,$tournament) {
        
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
            $time_live = str_replace("'", "", $match['time']);
            $matchSave->setMatchStatus("S");
          
        }  elseif ($match['time'] == "FT" ) {
            $time_live = $match['time'];
            $matchSave->setMatchStatus("F");
        } elseif ($match['time'] == "HT" || $match['time'] == "AET") {
            $time_live = $match['time'];
            $matchSave->setMatchStatus("S");
        } else {
            $time_live = 0;
            $matchSave->setMatchStatus("W");
        }
        $matchSave->setMatchTime($time_live);
        $matchSave->setMatchHomeScore(is_numeric($match['home_score']) ? $match['home_score'] : 0);
        $matchSave->setMatchAwayScore(is_numeric($match['away_score']) ? $match['away_score'] : 0);
        $matchSave->setMatchTournamentId($tournament->getTournamentId());
        $matchSave->setMatchLinkDetail($match['href_detail']);
        $matchSave->setMatchOrder(1);

        return $matchSave->save();
    }
}
 