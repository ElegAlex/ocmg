<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 25/06/18
 * Time: 15:37
 */

namespace AppBundle\DataFixtures;


use AppBundle\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class VilleFixtures
 * @package AppBundle\DataFixtures
 */
class VilleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $csv = fopen(__DIR__ . '../../../../tmp/laposte_hexasmal.csv', 'r');

        $i = 1;

        while (!feof($csv)) {
            $line = fgetcsv($csv);

            $ville[$i] = new Ville();

            $ville[$i]->setLibelleVille($line[1]);
            $ville[$i]->setCodePostal($this->getReference('code_postal-code_postal' . $i));


            $manager->persist($ville[$i]);

            $this->addReference('ville-ville' . $i, $ville[$i]);

            $i++;

        }

        fclose($csv);
        $manager->flush();

    }


    public function getDependencies()
    {
        return array(

            CodePostalFixtures::class

        );
    }
}