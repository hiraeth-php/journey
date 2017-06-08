<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Parsedown;
use Journey\Router;

class WelcomeAction
{
	/**
	 *
	 */
	protected $router = NULL;


	/**
	 *
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}


	/**
	 *
	 */
	public function __invoke(Parsedown $parsedown, Hiraeth\Application $app)
	{
		ob_start();

		?>
		<html>
			<head>
				<title>Welcome to Hiraeth Journey</title>
				<link href="hiraeth.css" rel="stylesheet" />
				<link href="prism.css" rel="stylesheet" />
				<script src="prism.js" type="text/javascript"></script>
			</head>
			<body>
				<?= $parsedown->text(file_get_contents($app->getFile('vendor/hiraeth/journey/README.md'))) ?>
			</body>
		</html>
		<?php

		return ob_get_clean();
	}
}
