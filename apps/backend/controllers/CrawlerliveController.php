<?php

namespace Score\Backend\Controllers;

use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\Http\Browser;
use Score\Models\ForexcecConfig;
use Score\Models\ForexcecLanguage;
use Score\Models\ScTeam;
use Score\Repositories\Config;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Repositories\Activity;
use Score\Repositories\Language;
use Score\Repositories\CrawlerScore;
use Score\Repositories\Team;

use Score\Models\ScMatch;
use Score\Repositories\MatchRepo;
use Score\Repositories\Tournament;
use Clue\React\Buzz\Browse;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerController extends ControllerBase
{

    public function indexAction()
    {
        $start_time_cron = time() + 0 * 24 * 60 * 60;
        echo "Start crawl data in " . $this->my->formatDateTime(time()) . "/n/r";
        $link =  'https://www.livescores.com';
        $param_time = "/football/{$this->my->formatDateYMD($start_time_cron)}/?tz=7";
        $param_live = "/football/live/?tz=7";
        $url = $link . $param_live;


        $client = new Client();
        $crawler = $client->request('GET', $url);
        $list_match = CrawlerScore::CrawlLivescores($crawler);
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
            $matchRepo->saveMatch($match, $home, $away, $tournament);
        }
        die();
    }
    public function detailAction()
    {
        echo "Start crawl data in " . $this->my->formatDateTime(time()) . "/n/r";
        $link =  'https://www.livescores.com';
        $param_live = "/football/germany/bundesliga/mainz-vs-borussia-dortmund/704822/?tz=7&tab=tracker";
        $url = $link . $param_live;

        $client = new client();
        $crawler = $client->request('GET', $url);
        $list_match = CrawlerScore::CrawlDetailTracker($crawler);

        die();
    }


    /**
     * Gets the full code source of HTML page even if using ajax
     *
     * In php a simple file_get_content or a curl request could do the job except if the page is build with dynamic content (ajax request).
     * In that case wa have to emulate a full browser behavior to get full HTML content generated by javascript.
     *
     * @param $url url to crawl
     * @return $html_content url html content
     */
    function get_code_source($url)
    {
        $html_content = null;

        # Decode url if needed
        $url = trim(urldecode($url));

        # Check url is not empty
        if ($url != '') {
            # Check http:// or https:// for further request or add it
            if (!stristr($url, 'http://') and !stristr($url, 'https://')) {
                $url = 'http://' . $url;
            }

            $url_segs = parse_url($url);

            # Check url contains a hostname
            if (isset($url_segs['host'])) {

                # Define usefull paths
                $here = dirname(__FILE__) . DIRECTORY_SEPARATOR;
                $bin_files = $here . 'bin' . DIRECTORY_SEPARATOR;
                $jobs = $here . 'jobs' . DIRECTORY_SEPARATOR;

                # Change Url to Filename
                $file_name = $this->sanitize($url) . ".html";

                # Check existence or create jobs directory
                if (!is_dir($jobs)) {
                    mkdir($jobs);
                    /*file_put_contents($jobs . 'index.php', '<?php exit(); ?>');*/
                }

                # Clean url
                $url = strip_tags($url);
                $url = str_replace(';', '', $url);
                $url = str_replace('"', '', $url);
                $url = str_replace('\'', '/', $url);
                $url = str_replace('<?', '', $url);
                $url = str_replace('<?', '', $url);
                $url = str_replace('\077', ' ', $url);

                # Protect url
                $url = escapeshellcmd($url);

                # Create phantomjs script
                $src = "
                var page = new WebPage();
                page.settings.userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:16.0) Gecko/20120815 Firefox/16.0';
                var fs = require('fs');
                page.onLoadFinished = function(status) {
                    fs.write('{$file_name}', page.content, 'w');
                    phantom.exit();
                }
                page.open('{$url}');
            ";

                # Create job file
                $job_file = $jobs . $url_segs['host'] . crc32($src) . '.js';

                # Fill in job file
                file_put_contents($job_file, $src);

                # Create executable command
                $exec = $bin_files . 'phantomjs ' . $job_file;

                # Protect shell special char
                $escaped_command = escapeshellcmd($exec);

                # Run phantomjs script
                exec($escaped_command);
         
                # Retrieve url code source
                $html_content = file_get_contents($here . $file_name);

                # Delete html file (or not ... depending on what you want to do)
                unlink($here . $file_name);

                # Delete job file
                unlink($job_file);

                # Delete job directory
                rmdir($jobs);
            }
        }

        return $html_content;
    }

    function sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array(
            "~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?"
        );
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
            $clean;
    }
}
