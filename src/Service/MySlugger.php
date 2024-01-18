<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    public function __construct(private SluggerInterface $slugger, private bool $toLower)
    {}

    public function slugTitle($title): string
    {
        if($this->toLower)
        {
            return $this->slugger->slug($title)->lower();
        }
        return $this->slugger->slug($title);
    }
}