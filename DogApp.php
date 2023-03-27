<?php
namespace GDO\Dog;

use GDO\Core\Application;

final class DogApp extends Application
{

	public function isCLI(): bool { return true; }

	public function isHTML(): bool { return false; }

	public function getFormat(): string { return 'cli'; }

}
