<?php

    namespace App\Utils;

    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class base64Converter extends UploadedFile
    {
/*
        public function __construct(string $base64String, string $originalName)
        {
            $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
            $data = base64_decode($base64String);
            file_put_contents($filePath, $data);
            $error = null;
            $mimeType = null;
            $test = true;

            parent::__construct($filePath, $originalName, $mimeType, $error, $test);
        }

*/
        public function getImageByName(string $libelle){
            $cheminImage = '../images/';

            // Lire le contenu de l'image
            $contenuImage = file_get_contents($cheminImage . $libelle);

            // Encoder l'image en base64
            $imageBase64 = base64_encode($contenuImage);

            return $imageBase64;
        }
    }
?>