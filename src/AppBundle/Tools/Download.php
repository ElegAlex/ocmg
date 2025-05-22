<?php


namespace AppBundle\Tools;


use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Download
{
    public static function downloadAction($folderPath,$filename){


        $response = new BinaryFileResponse($folderPath.$filename);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }
}