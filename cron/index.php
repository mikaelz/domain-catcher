<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use WebSupport\Client;
use WebSupport\DomainCatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$client = new Client($_ENV['WS_API_KEY'], $_ENV['WS_API_SECRET']);
$domainCatcher = new DomainCatcher($client);
$domain = '';
if (filter_var($_GET['domain'], FILTER_VALIDATE_DOMAIN)) {
	$domain = $_GET['domain'];
}
if (!empty($domain) && $domainCatcher->isDomainAvailable($domain)) {
	// Order domain
	return;
}

// Domain is not available
