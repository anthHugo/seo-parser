<?php
namespace Seo\Parser;

class Parser
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function process(string $string): string
    {
       $result = $string;
       foreach ($this->filters as $filter) {
           $result = $filter->apply($result);
       }
       return $result;
   }

   public function pipe(FilterInterface $filter)
   {
       $this->filters[] = $filter;
       return $this;
   }
}
