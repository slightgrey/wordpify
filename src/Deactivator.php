<?php

declare(strict_types=1);

namespace Wordpify;

use Wordpify\Sync\CronScheduler;

class Deactivator {

	public static function deactivate(): void {
		CronScheduler::clear();
		flush_rewrite_rules();
	}
}
