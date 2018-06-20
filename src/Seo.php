<?php
/**
 * @author      Hugo Casabella <hugo.casabella@gmail.com>
 * @license     MIT
 */
declare(strict_types = 1);

namespace Seo;

use \Seo\Parser\Parser;

class Seo
{
    private $title;

    private $description;

    private $keywords;

    private $data = [];

    public function __construct(
        string $title = '',
        string $description = '',
        string $keywords = '',
        array $data = []
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->data = $data;
    }

    public function getTitle(): string
    {
        return $this->parse($this->title);
    }

    public function getDescription(): string
    {
        return $this->parse($this->description);
    }

    public function getKeywords(): string
    {
        return $this->parse($this->keywords);
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'keywords' => $this->getKeywords(),
        ];
    }

    private function parse($string): string
    {
        return (new Parser())
                    ->pipe(new \Seo\Parser\InjectData($this->data))
                    ->pipe(new \Seo\Parser\Cleaner)
                    ->pipe(new \Seo\Parser\Truncate)
                    ->process($string);
    }
}
