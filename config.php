<?php

return [
	"title" => "New App",

	"routes" => [
		"/" => "Page",

		"/tests/db/get" => "Tests::dbGet",
		"/tests/db/insert" => "Tests::dbInsert"
	],

	"db" => [
		"host" => "localhost",
		"dbname" => "test",
		"user" => "root",
		"pass" => ""
	]
];