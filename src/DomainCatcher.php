<?php

declare(strict_types=1);

namespace WebSupport;

use JsonException;

class DomainCatcher
{
	private const string API_BASE = 'https://rest.websupport.sk';

	public function __construct(
		private readonly string $apiKey,
		private readonly string $apiSecret,
		private readonly int    $userId,
	)
	{
	}

	protected function request(string $method, string $path, array $data = [], string $query = ''): bool|string
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

	/**
	 * @throws JsonException
	 */
	public function isDomainAvailable(string $domain): bool
	{
		$response = $this->request('POST', '/v1/order/sk/validate/domain', ['domain' => $domain]);
		$response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
		$this->log(sprintf('Domain availability response for %s', $domain), $response['status'], $response['errors']);

		return $response['errors'] === [];
	}

	public function orderDomain(array $domains, bool $dryRun = true): bool
	{
		$data['services'] = [];
		foreach ($domains as $domain) {
			$data['services'][] = [
				'type' => 'domain',
				'domain' => $domain,
			];
		}
		$response = $this->request(
			'POST',
			sprintf('/v1/user/%s/order', $this->userId),
			$data,
			'?dry_run=' . ($dryRun ? '1' : '0'),
		);
		$response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
		$this->log(sprintf('Order response for %s', implode(', ', $domains)), $response['status'], $response);

		return $response['errors'] === [];
	}

	protected function log(string $message, string $status, array $context = []): void
	{
		file_put_contents(
			__DIR__ . '/../var/log/' . pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_FILENAME) . '.log',
			sprintf('%s %s %s %s' . PHP_EOL, date('c'), $status, $message, json_encode($context, JSON_THROW_ON_ERROR)),
			FILE_APPEND,
		);
	}
}
