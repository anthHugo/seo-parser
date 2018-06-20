<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo\Parser;

interface FilterInterface
{
    public function apply(string $string): string;
}
