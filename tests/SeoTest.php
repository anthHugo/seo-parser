<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo\Test;

use PHPUnit\Framework\TestCase;

class SeoTest extends TestCase
{
    public function testGetTitle()
    {
        $this->assertEquals(
            'Bienvenue',
            (new \Seo\Seo('Bienvenue'))->getTitle()
        );
    }

    public function testTrim()
    {
        $this->assertEquals(
            'Bienvenue sur votre site internet',
            $this->build('  Bienvenue sur votre site internet ')
        );
    }

    public function testTruncatedWordSafe()
    {
        $this->assertEquals(
            'Bienvenue sur la',
            $this->build('/Bienvenue sur la page de votre site internet/16')
        );
    }

    public function testParseData()
    {
        $this->assertEquals(
            'Coupe du monde en Russie - 20-06-2018',
            $this->buildData('{title} - {created}')
        );
    }

    private function build($title = '')
    {
        return (new \Seo\Seo($title))->getTitle();
    }

    private function buildData($title = '')
    {
        $data = require('schemas.php');

        return (new \Seo\Seo($title, 'description', 'keywords', $data))->getTitle();
    }
}
