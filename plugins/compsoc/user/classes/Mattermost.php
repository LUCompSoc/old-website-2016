<?php namespace Compsoc\User\Classes;

use Symfony\Component\Process\Process;
use ApplicationException;

class Mattermost
{
	public static function create_user($username, $email, $password)
	{
		$password = str_replace('$', '\$', $password);
		$password = str_replace('"', '\"', $password);
		$process = new Process('cd /var/www/mattermost/ && ./bin/platform '
		.	'-create_user '
		.	'-team_name="compsoc" '
		.	'-username="' . $username . '" '
		.	'-email="' . $email . '" '
		.	'-password="' . $password . '"'
		);

		$process->run();

		// executes after the command finishes
		if (!$process->isSuccessful()) {
			throw new ApplicationException('Mattermost account could not be created!');
		}

		return $process->getOutput();
	}
}