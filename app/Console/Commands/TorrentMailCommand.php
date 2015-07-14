<?php

namespace App\Console\Commands;

use App\Torrent;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class TorrentMailCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'torrent:mail';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email with new torrents.';

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
        $torrents = Torrent::where('seeders', '>', 29)->where('isSent', '=', false)->get();
        if(count($torrents)){
            Mail::send('emails.freshTorrents', ['torrents' => $torrents], function (Message $m) {
                $m->from(env("MAIL_FROM_ADDRESS"),'AxelPAL');
                $m->to(env("EMAIL_TO_SEND"), "AxelPAL")->subject('Новые торренты за ' . date("Y-m-d"));
            });
            foreach ($torrents as $torrent) {
                $torrent->isSent = true;
                $torrent->save();
            }
        }
	}
}
