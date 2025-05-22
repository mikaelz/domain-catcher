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
$domain = '';
if (filter_var($_GET['domain'], FILTER_VALIDATE_DOMAIN)) {
	$domain = $_GET['domain'];
}
if (!empty($domain) && $domainCatcher->isDomainAvailable($domain)) {
	$domainCatcher->orderDomain($domain, $_ENV['DRY_RUN'] === 'true');
}
