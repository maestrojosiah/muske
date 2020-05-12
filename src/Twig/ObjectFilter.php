<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ObjectFilter extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('cast_to_array', array($this, 'objectFilter')),
        );
    }

    public function objectFilter($stdClassObject) {

        $response = (array)$stdClassObject;
    
        return $response;

    }

    public function getName()
    {
        return 'cast_to_array';
    }
}

?>