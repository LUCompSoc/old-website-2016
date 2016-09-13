<?php namespace Compsoc\User\Classes;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Mattermost
{
	public static function create_user($username, $email, $password)
	{
		$process = new Process('cd /var/www/mattermost/ && ./bin/platform '
		.	'-create_user '
		.	'-team_name="compsoc" '
		.	'-username="' . $username . '" '
		.	'-email="' . $email . '" '
		.	'-password="' . escapeshellarg($password) . '"'
		);

		$process->run();

		// executes after the command finishes
		if (!$process->isSuccessful()) {
		    throw new ProcessFailedException('Mattermost account could not be created!');
		}

		return $process->getOutput();
	}
}