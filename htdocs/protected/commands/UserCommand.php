<?php

class UserCommand extends CConsoleCommand {
	public function actionIndex() {
		echo "You can use this command for basic user management.\n";
		echo "Right now, you can just create users.\n";
		echo "\n";
		echo $this->getHelp();
	}

	public function actionCreate( $username, $email, $password=NULL ) {
		if( $password == NULL ) {
			$password = '';
			$chrs = "abcdefghijklmnopqrstuvwxyz0123456789";
			for( $j = 0; $j < 6; $j++ ) {
				$password .= $chrs[mt_rand(0,35)];
			}
			echo "Randomly generated password: " . $password . "\n\n";
		}

		User::register(
			$username,
			$email,
			$password
		);
	}
}

