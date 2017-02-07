<?php
require 'vendor/autoload.php';
use Goutte\Client;

function main () {
	ea();
	neas();
}

function ea() {
	$client = new Client();
	$crawler = $client->request('GET', 'https://www.englishaustralia.com.au/college_courses.php?id=123');

	// get name, address
	$names = $crawler->filter('div[class^=expand_event] p > strong:nth-child(2)')->each(
		function ($node) {
			return $node->text();
		}
	);
	$addresses = $crawler->filter('div[class^=expand_event] p > strong:nth-child(9)')->each(
		function ($node) {
			return $node->html();
		}
	);

	// output
	$file = new SplFileObject('englishaustralia.csv', 'w');
	$file->fputcsv(['name', 'area', 'address']);
	foreach ($names as $k => $name) {
		$addresses[$k] = array_map('trim', explode('<br>', $addresses[$k]));
		$area = current(array_slice($addresses[$k], -3, 1));
		$file->fputcsv([
			'name' => $name,
			'area' => $area,
			'address' => str_replace('&amp;', '&', implode(', ', $addresses[$k])),
		]);
	}
}

function neas () {
	$client = new Client();
	$crawler = $client->request('GET', 'http://www.neas.org.au/studentsagents/centre-locator/?country=AU&name_search&num_per_page=9999');

	// get name, address
	$names = $crawler->filter('section.centre-locator > div:nth-child(1) h2')->each(
		function ($node) {
			return $node->text();
		}
	);
	$addresses = $crawler->filter('section.centre-locator > div:nth-child(1) > p')->each(
		function ($node) {
			return $node->text();
		}
	);

	// output
	$file = new SplFileObject('neas.csv', 'w');
	$file->fputcsv(['name', 'area', 'address']);
	foreach ($names as $k => $name) {
		$address = array_map('trim', explode(',', str_replace(',,', ',', $addresses[$k])));
		$name = str_replace('–', '-', $name);
		if (!is_numeric(end($address))) {
			// 表記ゆれ対応
			$end_address = str_replace('Australia ', '', end($address));
			if (preg_match('/^(.+) ([A-Z]{2,3}) ([0-9]{4})$/', $end_address, $last_address) === 0) {
				preg_match('/^([A-Z]{2,3}) ([0-9]{4})$/', $end_address, $last_address);
			}
			array_pop($address);
			array_shift($last_address);
			$address = array_merge($address, $last_address);
		}
		$area = current(array_slice($address, -3, 1));
		if (strpos($area, 'via ') !== false) {
			$area = current(array_slice($address, -4, 1));
		}
		$file->fputcsv([
			'name' => $name,
			'area' => $area,
			'address' => implode(', ', $address),
		]);
	}
}

main();
