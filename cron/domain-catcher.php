<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use WebSupport\DomainCatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$domainCatcher = new DomainCatcher(
	$_ENV['WS_API_KEY'],
	$_ENV['WS_API_SECRET'],
	(int)$_ENV['WS_USER_ID'],
);
$domains = [];
foreach ($_GET['domains'] as $domain) {
	if (filter_var($domain, FILTER_VALIDATE_DOMAIN) && $domainCatcher->isDomainAvailable($domain)) {
		$domains[] = $domain;
	}
}

if ($domains === []) {
	exit('No available domains found.');
}

$domainCatcher->orderDomain($domains, $_ENV['DRY_RUN'] === 'true');
