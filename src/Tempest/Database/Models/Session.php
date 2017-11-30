<?php namespace Tempest\Database\Models;

use Carbon\Carbon;
use Tempest\Database\Field;
use Tempest\Database\Model;

/**
 * A session stored in the database.
 *
 * @property int $id The session ID.
 * @property Carbon $created The time that the session was created.
 * @property Carbon $updated The time that the session was last updated.
 * @property string $ip The IP address of the session creator.
 * @property mixed $data The data stored in the session.
 *
 * @author Marty Wallace
 */
class Session extends Model {

	protected static function fields() {
		return [
			'id' => Field::string()->setAutoIncrements(),
			'created' => Field::dateTime()->setDefault('now'),
			'updated' => Field::dateTime()->setDefault('now'),
			'ip' => Field::string()->addIndex(),
			'data' => Field::text()->setNotNullable()
		];
	}

}