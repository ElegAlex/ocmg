<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 25/06/18
 * Time: 15:36
 */

namespace AppBundle\DataFixtures;


use AppBundle\Entity\CodePostal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CodePostalFixtures
 * @package AppBundle\DataFixtures
 */
class CodePostalFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $csv = fopen(__DIR__ . '../../../../tmp/laposte_hexasmal.csv', 'r');

        $i = 1;

        while (!feof($csv)) {
            $line = fgetcsv($csv);

            $code_postal[$i] = new CodePostal();

            $code_postal[$i]->setCode("'" . $line[2] . "'");

            $manager->persist($code_postal[$i]);

            $this->addReference('code_postal-code_postal' . $i, $code_postal[$i]);

            $i++;
        }

        fclose($csv);
        $manager->flush();

    }

}