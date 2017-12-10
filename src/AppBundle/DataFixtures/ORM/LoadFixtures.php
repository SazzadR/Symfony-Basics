<?php

namespace AppBundle\DataFixtures\ORM\Dev;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class LoadFixtures extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            __DIR__.'/fixtures.yml',
        ];
    }

    public function genus()
    {
        $genera = [
            'Octopus',
            'Balaena',
            'Orcinus',
            'Hippocampus',
            'Asterias',
            'Amphiprion',
            'Carcharodon',
            'Aurelia',
            'Cucumaria',
            'Balistoides',
            'Paralithodes',
            'Chelonia',
            'Trichechus',
            'Eumetopias'
        ];

        $key = array_rand($genera);

        return $genera[$key];
    }
}
