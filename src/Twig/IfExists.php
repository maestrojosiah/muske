<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IfExists extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('file_exists', [$this, 'exists']),
        );
    }


    public function exists($filename)
    {
        return file_exists($filename);
    }

}

?>
