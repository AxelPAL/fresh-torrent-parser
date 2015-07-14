<?php

namespace App\Http\Controllers;

use App\Torrent;
use App\TorrentCrawler;
use Goutte\Client;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	    $client = new Client();
	    $crawler = $client->request('GET', 'http://rustorka.com/forum/tracker.php?f[]=-1');
	    $form = $crawler->filter(".borderless.bCenter input")->selectButton('Вход')->form();
	    $crawler = $client->submit($form, array('login_username' => env('RUSTORKA_LOGIN'), 'login_password' => env('RUSTORKA_PASSWORD')));
	    $this->processPage($crawler);
	    $links = [];
	    $counter = 0;
	    $crawler->filter(".bottom_info .nav p a")->each(function(Crawler $node) use ($counter){
		    $counter++;
		    $links[] = $node->link();
	    });
	    var_dump($counter);exit;

    }

	public function processPage(Crawler $crawler) {
		$crawler->filter(".forumline.tablesorter tr")->each(function(Crawler $node){
			$filter = $node->filter("td");
			if(count($filter) && count($filter->eq(3))){
				$name = trim($filter->eq(3)->text());
				$downloadLink = trim($filter->eq(5)->filter('a')->attr('href'));
				$downloadLink = str_replace("./",'http://rustorka.com/',$downloadLink);
				$id = substr($downloadLink,strpos($downloadLink,"=") + 1);
				$seeders = trim($filter->eq(7)->text());
				$leechers = trim($filter->eq(8)->text());
				$downloadTimes = trim($filter->eq(9)->text());
				$torrent = Torrent::where('id', '=', $id)->first();
				if(!$torrent){
					$torrent = new Torrent();
				}
				$torrent->name = $name;
				$torrent->downloadLink = $downloadLink;
				$torrent->id = $id;
				$torrent->seeders = $seeders;
				$torrent->leechers = $leechers;
				$torrent->downloadTimes = $downloadTimes;
				$torrent->save();
			}
		});
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
