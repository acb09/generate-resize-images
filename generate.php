<?php

list($scriptname, $filename, $proporcions) = $argv;

$proporcions = explode(',', $proporcions);
$extension = pathinfo($filename, PATHINFO_EXTENSION);
$filenameWithoutExtensions = pathinfo($filename, PATHINFO_FILENAME);

if (!$filename)
    throw new RuntimeException('No filename given');
if (!$proporcions)
    throw new RuntimeException('No size given');
if (!count($proporcions))
    throw new RuntimeException('Size must be than zero');

foreach ($proporcions as $proporcion) {

    list($width, $height) = explode('x', $proporcion);

    // Obtendo o tamanho original
    list($width_orig, $height_orig) = getimagesize($filename);

    $width = floor($width);
    $height = floor($height);

    // Calculando a proporção
    $ratio_orig = $width_orig / $height_orig;

    if ($width / $height > $ratio_orig)
        $width = $height * $ratio_orig;
    else
        $height = $width / $ratio_orig;

    // O resize propriamente dito. Na verdade, estamos gerando uma nova imagem.
    $image_p = imagecreatetruecolor($width, $height);
    
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($filename);
            break;
        case 'png':
            $image = imagecreatefrompng($filename);
            break;
        case 'gif':
            $image = imagecreatefromgif($filename);
            break;
        default:
            throw new RuntimeException('Unsupported file type');
    }

    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    $width = floor($width);
    $height = floor($height);

    $filenameEnd = $filenameWithoutExtensions . '_' . $width . 'x' . $height . '.' . $extension;

    imagejpeg($image_p, $filenameEnd, 100);
}
