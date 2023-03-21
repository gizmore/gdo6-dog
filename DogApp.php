<?php
namespace GDO\Dog;

use GDO\Core\Application;

final class DogApp extends Application
{

	public function isCLI(): bool { return true; }

	public function isHTML() { return false; }

	public function getFormat() { return 'cli'; }

}
