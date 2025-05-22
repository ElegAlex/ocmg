<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 26/06/18
 * Time: 08:40
 */

namespace App\DataFixtures;


use App\Entity\Departement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DepartementFixtures
 * @package App\DataFixtures
 */
class DepartementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $csv = fopen(__DIR__ . '../../../../tmp/departement.csv', 'r');
//        /home/delepine/PhpstormProjects/OCMG2-0.0.0/src/AppBundle/DataFixtures/

        $i = 0;

        while (!feof($csv)) {
            $line = fgetcsv($csv);

            $departement[$i] = new Departement();

            //$departement[$i]->setRegion((int)$line[1]);

            $departement[$i]->setCodeDep($line[2]);

            $departement[$i]->setLibelleDep($line[3]);

            $departement[$i]->setIsActive(0);

            $manager->persist($departement[$i]);


            $this->addReference('departement-departement1' . $i, $departement[$i]);
            $this->addReference('departement-departement2' . $i, $departement[$i]);
            $this->addReference('departement-departement3' . $i, $departement[$i]);
            $this->addReference('departement-departement4' . $i, $departement[$i]);
            $this->addReference('departement-departement5' . $i, $departement[$i]);


            $i++;
        }

        fclose($csv);
        $manager->flush();

    }

}