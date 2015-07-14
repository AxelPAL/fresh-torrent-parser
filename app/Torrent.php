<?php

namespace App;

use Jenssegers\Mongodb\Model as Model;

class Torrent extends Model
{
	protected $collection = 'torrents';
    //

	public static function findOrCreate($id)
	{
		$obj = static::find($id);
		return $obj ?: new static;
	}
}
