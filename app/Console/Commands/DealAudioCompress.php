<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tools\AudioUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class DealAudioCompress extends Command
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'DealAudioCompress {--day=1}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Display an DealAudioCompress quote';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle ()
	{
		$this->info(base_path("vendor/getid3/getid3.php"));
		include base_path("vendor/getid3/getid3.php") . "";

		//获取需处理列表
		$day = $this->option('day');
		$this->info("day=" . $day);

		$start_time = Utils::getTime(-86400 * $day);
//		$audio_list = AudioUtils::getDealAudioList($start_time);
		$audio_list = AudioUtils::getLengthAudioList();
		if ($audio_list && count($audio_list) > 0) {
			foreach ($audio_list as $item) {
				$table_name   = 't_audio';
				$app_id       = $item->app_id;
				$id           = $item->id;
				$audio_url    = $item->audio_url;
				$audio_length = $item->audio_length;

				AudioUtils::SingleAudioCompress($table_name, $app_id, $id, $audio_url, $audio_length);
			}
		}
	}
}
