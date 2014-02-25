<?php

namespace tempest\mysql;

use \tempest\mysql\Database;


abstract class Model
{

	abstract function validate(Database $db);
	abstract function commit(Database $db);

}