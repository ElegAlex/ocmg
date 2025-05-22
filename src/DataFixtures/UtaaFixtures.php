<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 25/06/18
 * Time: 15:37
 */

namespace App\DataFixtures;


use App\Entity\Utaa;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UtaaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $csv = fopen(__DIR__ . '/../../tmp/departement.csv', 'r');
        $i = 0;
        while (!feof($csv)) {

            $line = fgetcsv($csv);
            $utaa1[$i] = new Utaa();
            $utaa2[$i] = new Utaa();
            $utaa3[$i] = new Utaa();
            $utaa4[$i] = new Utaa();
            $utaa5[$i] = new Utaa();


            $utaa1[$i]->setDepartement($this->getReference('departement-departement1' . $i));
            $utaa1[$i]->setCodeUtaa($line[2] . '1');
            $utaa2[$i]->setDepartement($this->getReference('departement-departement2' . $i));
            $utaa2[$i]->setCodeUtaa($line[2] . '2');
            $utaa3[$i]->setDepartement($this->getReference('departement-departement3' . $i));
            $utaa3[$i]->setCodeUtaa($line[2] . '3');
            $utaa4[$i]->setDepartement($this->getReference('departement-departement4' . $i));
            $utaa4[$i]->setCodeUtaa($line[2] . '4');
            $utaa5[$i]->setDepartement($this->getReference('departement-departement5' . $i));
            $utaa5[$i]->setCodeUtaa($line[2] . '5');


            $manager->persist($utaa1[$i]);
            $manager->persist($utaa2[$i]);
            $manager->persist($utaa3[$i]);
            $manager->persist($utaa4[$i]);
            $manager->persist($utaa5[$i]);

            $i++;


        }

        fclose($csv);
        $manager->flush();
    }


    public function getDependencies()
    {

        return array(

            DepartementFixtures::class

        );

    }
}