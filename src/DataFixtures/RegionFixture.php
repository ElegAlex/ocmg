<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 24/10/18
 * Time: 15:23
 */

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RegionFixture
 * @package App\DataFixtures
 */
class RegionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $csv = fopen(__DIR__ . '../../../../tmp/region.csv', 'r');

        $i = 0;

        while (!feof($csv)) {
            $line = fgetcsv($csv);

            $region[$i] = new Region();
            $region[$i]->setId($line[0]);
            $region[$i]->setLibelleRegion($line[1]);
            $region[$i]->setIsActive(0);

            $manager->persist($region[$i]);

            $i++;
        }

        fclose($csv);
        $manager->flush();

    }


}