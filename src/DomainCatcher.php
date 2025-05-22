<?php

declare(strict_types=1);

namespace WebSupport;

use JsonException;

class DomainCatcher
{
	public function __construct(private readonly Client $client)
	{
	}

	/**
	 * @throws JsonException
	 */
	public function isDomainAvailable(string $domain): bool
	{
		$response = $this->client->request('POST', '/order/sk/validate/domain', ['domain' => $domain]);
		$data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

		return $data['errors'] === [];
	}
}
