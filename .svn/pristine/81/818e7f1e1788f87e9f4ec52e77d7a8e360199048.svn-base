<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tools\AudioUtils;
use App\Http\Controllers\Tools\UserUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class UpdateUserWxAvatar extends Command
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'UpdateUserWxAvatar {time?} {--offset=0}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Display an UpdateUserWxAvatar quote';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle ()
	{
		ini_set('memory_limit', '1024M');
		$offset = $this->option('offset');
		$this->info("offset=" . $offset);

		$time = $this->argument('time');
		if (empty($time)) {
			$time = Utils::getTime();
		}

		$start_time = date('Y-m-01 00:00:00', strtotime($time));
		$end_time   = date('Y-m-01 00:00:00', strtotime("+1 month", strtotime($time)));
		$this->info($start_time . "~" . $end_time);

		while (true) {
			//获取需处理列表
			$userList      = UserUtils::getOldWxAvatarUsers($start_time, $end_time, $offset);
			$userListCount = !empty($userList) ? count($userList) : 0;
			$this->info($start_time . "~" . $end_time . " user_count=" . $userListCount);

			if ($userListCount > 0) {
				UserUtils::updateDbUserAvatar($userList);
				//                $expire_list_count = !empty($expire_list) ? count($expire_list) : 0;
				//                $this->info("expire_count=".$expire_list_count);
				//                if ($expire_list_count > 0) {
				//                    UserUtils::updateWxUserAvatar($expire_list);
				//                }
			} else {
				break;
			}
		}
		$this->info($start_time . "~" . $end_time . " end");
	}
}
