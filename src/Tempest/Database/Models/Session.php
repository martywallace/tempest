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
 * @property mixed $data The data stored in the session.
 *
 * @author Marty Wallace
 */
class Session extends Model {

	protected static function fields() {
		return [
			'id' => Field::string()->primary(),
			'created' => Field::dateTime()->default('now'),
			'updated' => Field::dateTime()->default('now'),
			'data' => Field::text()
		];
	}

}