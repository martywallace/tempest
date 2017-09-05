<?php namespace Tempest\Extensions;

use Carbon\Carbon;
use Tempest\Database\Models\Session;
use SessionHandlerInterface;

/**
 * Manages storing and retrieving session data from the database.
 *
 * @author Marty Wallace
 */
class DbSessionHandler implements SessionHandlerInterface {

	public function close() {
		return true;
	}

	public function destroy($id) {
		Session::delete()->where('id', $id)->execute();

		return true;
	}

	public function gc($lifetime) {
		Session::delete()
			->whereGreater('updated', Carbon::now()->subSeconds($lifetime)->toDateTimeString())
			->execute();

		return true;
	}

	public function open($path, $name) {
		return true;
	}

	public function read($id) {
		/** @var Session $session */
		$session = Session::find($id);

		return !empty($session) ? $session->data : '';
	}

	public function write($id, $data) {
		$session = Session::find($id);

		if (!empty($session)) {
			$session->fill([
				'updated' => Carbon::now(),
				'data' => $data
			])->save();
		} else {
			Session::create([
				'id' => $id,
				'updated' => Carbon::now(),
				'data' => $data
			])->save();
		}

		return true;
	}

}