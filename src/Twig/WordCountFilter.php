<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class WordCountFilter extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('word_count', array($this, 'getWordCount')),
        );
    }

    public function getName()
    {
        return 'word_count_filter';
    }

    public function getWordCount(string $string)
    {
        return str_word_count($string);
    }
}
