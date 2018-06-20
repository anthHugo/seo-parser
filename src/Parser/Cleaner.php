<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo\Parser;

class Cleaner implements FilterInterface
{
    public function apply(string $string): string
    {
		$last = mb_substr($string, -1, 1);

		if (in_array($last, ['-', ',', ';', '.', ':', '!', '/', '('])) {
			$string = mb_substr($string, 0, mb_strlen($string) - 1 );
		}

        return trim($string);
    }
}
