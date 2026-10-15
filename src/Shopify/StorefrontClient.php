<?php

declare(strict_types=1);

namespace Wordpify\Shopify;

use Wordpify\Options;

class StorefrontClient {

	const API_VERSION = '2026-07';

	private string $endpoint;
	private string $token;

	public function __construct( string $store_domain = '', string $access_token = '' ) {
		if ( $store_domain && $access_token ) {
			$this->endpoint = "https://{$store_domain}/api/" . self::API_VERSION . "/graphql.json";
			$this->token    = $access_token;
		} else {
			$connection     = Options::get_connection();
			$this->endpoint = "https://{$connection['store_domain']}/api/" . self::API_VERSION . "/graphql.json";
			$this->token    = $connection['access_token'];
		}
	}

	/**
	 * Execute a Storefront API GraphQL query or mutation.
	 *
	 * @throws \RuntimeException on HTTP or GraphQL errors.
	 */
	public function query( string $gql, array $variables = [] ): array {
		$args = [
			'timeout' => 30,
			'headers' => [
				'Content-Type'                      => 'application/json',
				'X-Shopify-Storefront-Access-Token' => $this->token,
			],
			'body'    => wp_json_encode( array_filter( [
				'query'     => $gql,
				'variables' => $variables ?: null,
			] ) ),
		];

		$attempts = 0;
		$response = null;

		do {
			$response = wp_remote_post( $this->endpoint, $args );

			if ( is_wp_error( $response ) ) {
				throw new \RuntimeException( 'Shopify API request failed: ' . $response->get_error_message() );
			}

			$code = wp_remote_retrieve_response_code( $response );

			if ( $code === 429 && $attempts < 3 ) {
				$retry_after = (int) ( wp_remote_retrieve_header( $response, 'retry-after' ) ?: 2 );
				sleep( min( $retry_after, 10 ) );
				$attempts++;
				continue;
			}

			break;
		} while ( true );

		if ( $code < 200 || $code >= 300 ) {
			throw new \RuntimeException( "Shopify API returned HTTP {$code}" );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $data['errors'] ) ) {
			$msg = $data['errors'][0]['message'] ?? 'Unknown GraphQL error';
			throw new \RuntimeException( 'Shopify GraphQL error: ' . $msg );
		}

		return $data['data'] ?? [];
	}

	/**
	 * Test the connection by fetching the shop name.
	 *
	 * @return array{connected: bool, shop_name: string, error: string}
	 */
	public function test_connection(): array {
		try {
			$data = $this->query( '{ shop { name } }' );
			return [
				'connected' => true,
				'shop_name' => $data['shop']['name'] ?? '',
				'error'     => '',
			];
		} catch ( \RuntimeException $e ) {
			return [
				'connected' => false,
				'shop_name' => '',
				'error'     => $e->getMessage(),
			];
		}
	}
}
