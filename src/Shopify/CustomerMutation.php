<?php

declare(strict_types=1);

namespace Wordpify\Shopify;

class CustomerMutation {

	public static function create(): string {
		return <<<'GQL'
		mutation CustomerCreate($input: CustomerCreateInput!) {
			customerCreate(input: $input) {
				customer { id email firstName lastName }
				customerUserErrors { code field message }
			}
		}
		GQL;
	}

	public static function access_token_create(): string {
		return <<<'GQL'
		mutation CustomerAccessTokenCreate($input: CustomerAccessTokenCreateInput!) {
			customerAccessTokenCreate(input: $input) {
				customerAccessToken { accessToken expiresAt }
				customerUserErrors { code field message }
			}
		}
		GQL;
	}

	public static function access_token_delete(): string {
		return <<<'GQL'
		mutation CustomerAccessTokenDelete($customerAccessToken: String!) {
			customerAccessTokenDelete(customerAccessToken: $customerAccessToken) {
				deletedAccessToken
				userErrors { field message }
			}
		}
		GQL;
	}

	public static function get_customer(): string {
		return <<<'GQL'
		query GetCustomer($customerAccessToken: String!) {
			customer(customerAccessToken: $customerAccessToken) {
				id
				email
				firstName
				lastName
				phone
				defaultAddress { id address1 address2 city province country zip }
			}
		}
		GQL;
	}

	public static function update(): string {
		return <<<'GQL'
		mutation CustomerUpdate($customerAccessToken: String!, $customer: CustomerUpdateInput!) {
			customerUpdate(customerAccessToken: $customerAccessToken, customer: $customer) {
				customer { id email firstName lastName phone }
				customerUserErrors { code field message }
			}
		}
		GQL;
	}

	public static function get_orders(): string {
		return <<<'GQL'
		query GetCustomerOrders($customerAccessToken: String!, $cursor: String) {
			customer(customerAccessToken: $customerAccessToken) {
				orders(first: 10, after: $cursor) {
					pageInfo { hasNextPage endCursor }
					nodes {
						id
						orderNumber
						processedAt
						financialStatus
						fulfillmentStatus
						currentTotalPrice { amount currencyCode }
						lineItems(first: 20) {
							nodes {
								title
								quantity
								variant {
									id
									title
									price { amount currencyCode }
									image { url altText }
								}
							}
						}
					}
				}
			}
		}
		GQL;
	}

	public static function get_addresses(): string {
		return <<<'GQL'
		query GetCustomerAddresses($customerAccessToken: String!) {
			customer(customerAccessToken: $customerAccessToken) {
				addresses(first: 20) {
					nodes {
						id
						firstName
						lastName
						address1
						address2
						city
						province
						country
						zip
						phone
					}
				}
			}
		}
		GQL;
	}

	public static function address_create(): string {
		return <<<'GQL'
		mutation CustomerAddressCreate($customerAccessToken: String!, $address: MailingAddressInput!) {
			customerAddressCreate(customerAccessToken: $customerAccessToken, address: $address) {
				customerAddress { id }
				customerUserErrors { code field message }
			}
		}
		GQL;
	}

	public static function address_update(): string {
		return <<<'GQL'
		mutation CustomerAddressUpdate($customerAccessToken: String!, $id: ID!, $address: MailingAddressInput!) {
			customerAddressUpdate(customerAccessToken: $customerAccessToken, id: $id, address: $address) {
				customerAddress { id }
				customerUserErrors { code field message }
			}
		}
		GQL;
	}

	public static function address_delete(): string {
		return <<<'GQL'
		mutation CustomerAddressDelete($customerAccessToken: String!, $id: ID!) {
			customerAddressDelete(customerAccessToken: $customerAccessToken, id: $id) {
				deletedCustomerAddressId
				customerUserErrors { code field message }
			}
		}
		GQL;
	}
}
