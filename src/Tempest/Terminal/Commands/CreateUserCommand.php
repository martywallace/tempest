<?php namespace Tempest\Terminal\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tempest\Database\Models\User;

/**
 * Manages {@link User user} creation.
 *
 * @author Ascension Web Development
 */
class CreateUserCommand extends Command {

	protected function configure() {
		$this
			->setName('user:create')
			->setDescription('Create a new user within the database.')
			->addArgument('email', InputArgument::REQUIRED, 'The email address for the new user.')
			->addArgument('password', InputArgument::REQUIRED, 'The password for the new user.');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$user = User::create([
			'email' => strtolower($input->getArgument('email')),
			'password' => password_hash($input->getArgument('password'), CRYPT_BLOWFISH)
		]);

		if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
			$output->writeln('The user email address must be valid.');
		}

		$user->save(false);

		$output->writeln([
			'New user <' . $user->email . '> created!',
			'ID: ' . $user->id,
			'X-User-Token: ' . User::createXUserToken($user->email, $input->getArgument('password'))
		]);
	}

}