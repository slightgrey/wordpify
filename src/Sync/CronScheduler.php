<?php

declare(strict_types=1);

namespace Wordpify\Sync;

class CronScheduler {

	const EVENT = 'wordpify_sync_products';

	public static function schedule( string $interval ): void {
		if ( $interval === 'off' ) {
			self::clear();
			return;
		}

		if ( ! wp_next_scheduled( self::EVENT ) ) {
			wp_schedule_event( time(), $interval, self::EVENT );
		}
	}

	public static function clear(): void {
		$timestamp = wp_next_scheduled( self::EVENT );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::EVENT );
		}
	}

	public static function reschedule( string $interval ): void {
		self::clear();
		self::schedule( $interval );
	}
}
