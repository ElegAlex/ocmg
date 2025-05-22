<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 13/11/18
 * Time: 11:03
 */

namespace AppBundle\Tools;


class JsonHandler
{

    /**
     *
     * méthode permet de créer un tableau champs+ datas
     *
     * @param array $header
     * @param array $datas
     * @return array
     */
    public static function mergeData(array $header, array $datas): array
    {
        return array_combine($header, $datas);
    }


}