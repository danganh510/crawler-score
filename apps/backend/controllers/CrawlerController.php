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
use Score\Repositories\MatchRepo;
use Score\Repositories\Tournament;

class CrawlerController extends ControllerBase
{

    public function indexAction()
    {
        $start_time_cron = time() + 0 * 24*60*60;
        echo "Start crawl data in ".$this->my->formatDateTime(time()) ."/n/r";
        $link =  'https://www.livescores.com';
        $param_time = "/football/{$this->my->formatDateYMD($start_time_cron )}/?tz=7";
        $param_live = "/football/live/?tz=7";
        $url = $link.$param_live;
      
        $client = new client();
        $crawler = $client->request('GET', $url);
        $list_match = CrawlerScore::Crawl($crawler);
        $matchRepo = new MatchRepo();
        foreach ($list_match as $match) {
            $home = Team::findByName($match['home']);
            if (!$home) {
                $home = Team::saveTeam($match['home'], $match['home_svg']);
            }
            $away = Team::findByName($match['away']);
            if (!$away) {
                $away = Team::saveTeam($match['away'], $match['away_svg']);
            }
            $tournament = Tournament::findByName($match['tournament']['tournament']);
            if (!$tournament) {
                $tournament = Tournament::saveTournament($match['tournament']);
            }
            if (!$home) {
                echo "can't save home team";
                continue;
            }
            if (!$away) {
                echo "can't save away team";
                continue;
            }
            if (!$tournament) {
                echo "can't save tournament team";
                continue;
            }
            $matchRepo->saveMatch($match, $home, $away,$tournament);         
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