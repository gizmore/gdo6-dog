<?php
declare(strict_types=1);
namespace GDO\Dog;

/**
 * Change a text string to weird utf8 codes, so it looks the same but is not the same data.
 * This is for removing highlighting of users in certain messages.
 * As a fallback, a softhyphen is inserted randomly.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author noother
 */
final class Obfuscate
{

	private const SOFTHYPHEN = "\xC2\xAD";

	private static array $UNICODE_LOOKALIKES = [
		'A' => "\xce\x91", 'B' => "\xce\x92", 'C' => "\xd0\xa1", 'E' => "\xce\x95", 'F' => "\xcf\x9c",
		'H' => "\xce\x97", 'I' => "\xce\x99", 'J' => "\xd0\x88", 'K' => "\xce\x9a", 'M' => "\xce\x9c",
		'N' => "\xce\x9d", 'O' => "\xce\x9f", 'P' => "\xce\xa1", 'S' => "\xd0\x85", 'T' => "\xce\xa4",
		'X' => "\xce\xa7", 'Y' => "\xce\xa5", 'Z' => "\xce\x96",

		'a' => "\xd0\xb0", 'c' => "\xd1\x81", 'e' => "\xd0\xb5", 'i' => "\xd1\x96", 'j' => "\xd1\x98",
		'o' => "\xd0\xbe", 'p' => "\xd1\x80", 's' => "\xd1\x95", 'x' => "\xd1\x85", 'y' => "\xd1\x83",
	];

	public static function obfuscate(string $string): string
	{
		return self::placeUnicode($string) ??
			self::placeSofthyphen($string);
	}

	private static function placeUnicode(string $string): ?string
	{
		for ($i = 0; $i < mb_strlen($string); $i++)
		{
			$char = mb_substr($string, $i, 1);
			if (isset(self::$UNICODE_LOOKALIKES[$char]))
			{
				return mb_substr($string, 0, $i) .
					self::$UNICODE_LOOKALIKES[$char] .
					mb_substr($string, $i + 1);
			}
		}
		return null;
	}

	private static function placeSofthyphen(string $string): string
	{
		$pos = rand(1, mb_strlen($string) - 1);
		return mb_substr($string, 0, $pos) . self::SOFTHYPHEN . mb_substr($string, $pos);
	}

}
