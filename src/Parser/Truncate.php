<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo\Parser;

class Truncate implements FilterInterface
{
    public function apply(string $string): string
    {
		return preg_replace_callback(
            '/\/(.*?)\/([0-9]+)/',
            [$this, 'callback'],
            $string
        );
    }

    private function callback(array $matches = [])
    {
        $valeur = (string) $matches[1];
        $limit = (int) $matches[2];

		if (mb_strlen($valeur) <= $limit) {
            return $valeur;
        }

        $truncated = mb_substr($valeur, 0, $limit + 1);

		if (mb_strlen($truncated) > $limit) {
			$spacepos = mb_strrpos($truncated, ' ');

			if (isset($spacepos)) {
				$truncated = trim(mb_substr($truncated, 0, $spacepos));
			}
		}

		return trim($truncated);
    }
}
