<?php

namespace App\Console\Commands;

use App\Torrent;
use Goutte\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class TorrentCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'torrent:parse';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parse new torrents.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $client = new Client();
        $crawler = $client->request('GET', 'http://rustorka.com/forum/tracker.php?f[]=-1');
        $form = $crawler->filter(".borderless.bCenter input")->selectButton('Вход')->form();
        $crawler = $client->submit($form, array('login_username' => env('RUSTORKA_LOGIN'), 'login_password' => env('RUSTORKA_PASSWORD')));
        $this->processPage($crawler);
        $links = [];
        $links = $crawler->filter("div.bottom_info a")->each(function(Crawler $node) use ($links){
            $links = $node->link();
            return $links;
        });
        if($links){
            foreach ($links as $key => $link) {
                if($key > 0){
                    $crawler = $client->click($link);
                    $this->processPage($crawler);
                }
            }
        }
	}

    public function processPage(Crawler $crawler) {
        $crawler->filter(".forumline.tablesorter tr")->each(function(Crawler $node){
            $filter = $node->filter("td");
            if(count($filter) && count($filter->eq(3))){
                $name = trim($filter->eq(3)->text());
                $torrentLink = trim($filter->eq(3)->filter('a')->attr('href'));
                $torrentLink = str_replace("./",'http://rustorka.com/forum/',$torrentLink);
                $id = substr($torrentLink,strpos($torrentLink,"=") + 1);
                $seeders = trim($filter->eq(7)->text());
                $leechers = trim($filter->eq(8)->text());
                $downloadTimes = trim($filter->eq(9)->text());
                $torrent = Torrent::where('id', '=', $id)->first();
                if(!$torrent){
                    $torrent = new Torrent();
                }
                $torrent->name = $name;
                $torrent->torrentLink = $torrentLink;
                $torrent->id = (int)$id;
                $torrent->seeders = (int)$seeders;
                $torrent->leechers = (int)$leechers;
                $torrent->downloadTimes = (int)$downloadTimes;
                $torrent->isSent = false;
                $torrent->save();
            }
        });
    }
}
