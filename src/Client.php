<?php

declare(strict_types=1);

namespace WebSupport;

use JsonException;

class Client
{
	private const string API_BASE = 'https://rest.websupport.sk/v1';

	public function __construct(private readonly string $apiKey, private readonly string $apiSecret)
	{
	}

	/**
	 * @throws JsonException
	 */
	public function request(string $method, string $path, array $data = [], string $query = ''): bool|string
	{
		$time = time();
		$canonicalRequest = sprintf('%s %s %s', $method, $path, $time);
		$signature = hash_hmac('sha1', $canonicalRequest, $this->apiSecret);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, sprintf('%s%s%s', self::API_BASE, $path, $query));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ':' . $signature);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Date: ' . gmdate('Ymd\THis\Z', $time),
		]);
		if ($data !== []) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
		}

		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			error_log(curl_error($ch));
		}
		curl_close($ch);

		return $response;
	}
}
