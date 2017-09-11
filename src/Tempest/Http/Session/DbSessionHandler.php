<?php namespace Tempest\Http\Session;

use Carbon\Carbon;
use Tempest\Database\Models\Session;

/**
 * Manages storing and retrieving session data from the database.
 *
 * @author Marty Wallace
 */
class DbSessionHandler extends BaseSessionHandler {

	public function destroy($id) {
		Session::delete()->where('id', $id)->execute();

		return true;
	}

	public function gc($lifetime) {
		Session::delete()
			->whereLessOrEqual('updated', Carbon::now()->subSeconds($lifetime)->toDateTimeString())
			->execute();

		return true;
	}

	public function read($id) {
		/** @var Session $session */
		$session = Session::find($id);

		return !empty($session) ? $session->data : '';
	}

	public function write($id, $data) {
		Session::findOrCreate($id, [
			'id' => $id,
			'updated' => Carbon::now(),
			'ip' => $this->getRequest() ? $this->getRequest()->getIP() : null,
			'data' => $data
		])->save();

		return true;
	}

}