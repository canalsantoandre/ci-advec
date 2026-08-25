<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

class Image extends BaseController
{
    public function vitoria()
    {

        $config = new App();
        $path = ROOTPATH . $config->pathUploadFile;

        $nome = "ALAFY";
        $sobrenome = "PRADO";

        $layer_lut = $path . 'cultos/lut.png';
        $layer_fader = $path . 'cultos/fader.png';
        $layer_endereco = $path . 'cultos/endereco.png';

        $background = $path . 'bkg.png';
        $layer_nome_culto = $path . 'cultos/03-culto-vitoria.png';
        $layer_convidado = $path . 'convidados/paloma-gomes.png'; //pr-nerildo-accioly1.png';

        $widthImage = 1920;
        $heightImage = 1080;

        $layer_1 = imagecreatefrompng($background);
        /***********************************/
        list($width, $height) = getimagesize($background);
        $newwidth = intval($width * 1.5);
        $newheight = intval($height * 1.5);

        $foto = imagecreatetruecolor($newwidth, $newheight);
        // Redimenciona
        imagecopyresized($foto, $layer_1, 0, 0, 0, 0, $widthImage, $heightImage, $width, $height);
        /***********************************/


        $layer_2 = imagecreatefrompng($layer_lut);
        $layer_3 = imagecreatefrompng($layer_nome_culto);
        $layer_4 = imagecreatefrompng($layer_convidado);
        list($wdl4, $htl4) = getimagesize($layer_convidado);

        $layer_5 = imagecreatefrompng($layer_fader);
        $layer_6 = imagecreatefrompng($layer_endereco);

        // monta ordem dos layers
        $this->imagecopymerge_alpha($layer_1, $layer_2, 0, 0, 0, 0, $widthImage, $heightImage, 100);
        $this->imagecopymerge_alpha($layer_1, $layer_3, 0, 0, 0, 0, $widthImage, $heightImage, 100);
        $this->imagecopymerge_alpha($layer_1, $layer_4, intval(($widthImage - $wdl4) / 2), $heightImage - $htl4, 0, 0, $wdl4, $htl4, 100);
        $this->imagecopymerge_alpha($layer_1, $layer_5, 0, 0, 0, 0, $widthImage, $heightImage, 110);
        $this->imagecopymerge_alpha($layer_1, $layer_6, 0, 0, 0, 0, $widthImage, $heightImage, 100);

        imagesavealpha($layer_1, true);

        /****************************************************************** */
        /* definir os textos da imagem  imagecolorallocate(img, RGB) */
        $navy = imagecolorallocate($layer_1, 255, 255, 255);
        $font_size = 22;
        //PRINTA O NOME
        $font_path = "fonts/Helvetica/HelveticaNowDisplay-ExtBlk.ttf";
        $text = $this->wrapText(strtoupper($nome), $font_size, $font_path);
        $leftText = 300; 
        $topText = intval(imageSY($layer_1) / 2) + 100;
        imagettftext(
            image: $layer_1,
            size: $font_size,
            angle: 0,
            x: intval($leftText),
            y: intval($topText),
            color: $navy,
            font_filename: $font_path,
            text: trim($text)
        );

        //PRINTA O SOBRENOME        
        $font_path = "fonts/Helvetica/HelveticaNowDisplay-Regular.ttf";
        $text = $this->wrapText(strtoupper($sobrenome), $font_size, $font_path);
        $topText = $topText +  $font_size * 1.25;
        imagettftext(
            image: $layer_1,
            size: $font_size,
            angle: 0,
            x: intval($leftText),
            y: intval($topText ),
            color: $navy,
            font_filename: $font_path,
            text: trim($text)
        );


        /****************************************************************** */

        $binary = imagepng($layer_1);

        return $this->response
            ->setHeader('Content-Type', 'image/png')
            ->setStatusCode(200)
            ->setBody($binary);

    }

    public function index()
    {


        $profile_pic = "https://manychat.com/ava/802879/470469428/016a5cd1eda25fda4a9ff9ca4df1ac0f";
        $ig_username = "TESTE";
        $nome = $ig_username; //$_GET["name"];


        $config = new App();
        $path = ROOTPATH . $config->pathUploadFile;

        $bg = $path . 'aviva-1.png';
        $bg2 = $path . 'aviva-2.png';
        $bg3 = $path . 'aviva-3.png';
        $mf = $path . 'moldura-foto.png';

        $fg = $path . 'fotos/' . $nome . '.jpg';

        $imageurl = $profile_pic; //$_GET['url'];
        $image = imagecreatefromjpeg($imageurl);
        imagejpeg($image, $fg);

        /***********************************/
        // Obtém novos tamanhos
        list($width, $height) = getimagesize($fg);
        $newwidth = intval($width * 1.5);
        $newheight = intval($height * 1.5);

        // Carrega
        $foto = imagecreatetruecolor($newwidth, $newheight);

        // Redimensiona
        imagecopyresized($foto, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        /***********************************/
        imagedestroy($image);



        if (file_exists($bg) && file_exists($fg)) {
            // Create image instances
            $dest = imagecreatefrompng($bg);
            $wdD = imageSX($dest);
            $htD = imageSY($dest);

            //$foto = imagecreatefromjpeg($foto);
            $dest2 = imagecreatefrompng($bg2);
            $dest3 = imagecreatefrompng($bg3);
            $moldura = imagecreatefrompng($mf);

            $wd = imageSX($foto);
            $ht = imageSY($foto);

            //Tamanho do fundo
            $top = intval((imageSY($dest) - $ht) / 2) - 60;
            $left = intval((imageSX($dest) - $wd) / 2);


            // monta ordem dos layers
            $this->imagecopymerge_alpha($dest, $moldura, 0, 0, 0, 0, $wdD, $htD, 100);
            $this->imagecopymerge_alpha($dest, $foto, $left, $top, 0, 0, $wd, $ht, 80);

            $this->imagecopymerge_alpha($dest, $dest2, 0, 0, 0, 0, $wdD, $htD, 100);
            $this->imagecopymerge_alpha($dest, $dest3, 0, 0, 0, 0, $wdD, $htD, 100);


            imagesavealpha($dest, true);
            // Output and free from memory


            // Add the text to the imag
            $navy = imagecolorallocate($image, 3, 5, 58);
            $font_size = 20;
            $font_path = "fonts/font.ttf";
            $font_path = "fonts/Helvetica/HelveticaNowDisplay-Regular.ttf";
            //$font = file_get_contents("http://themes.googleusercontent.com/static/fonts/abel/v3/RpUKfqNxoyNe_ka23bzQ2A.ttf");
            //file_put_contents( $font_path , $font);
            $text = $this->wrapText("@" . $nome, $font_size, $font_path);


            // This is our cordinates for X and Y para o texto (centraliza o texto)
            $bbox = imageftbbox($font_size, 0, $font_path, $text);
            $leftText = $bbox[0] + (imageSX($dest) / 2) - ($bbox[4] / 2) - 5;
            $topText = $top;//- $ht;
            //$y = 50 + $bbox[1] + (imageSY($im) / 2) - ($bbox[5] / 2) - 5;

            imagettftext(
                image: $dest,
                size: $font_size,
                angle: 0,
                x: intval($leftText),
                y: intval($topText - ($font_size * 2)),
                color: $navy,
                font_filename: $font_path,
                text: trim($text)
            );

            imagettftext(
                image: $dest,
                size: $font_size + 10,
                angle: 0,
                x: intval($leftText),
                y: intval($topText - ($font_size * 4)),
                color: $navy,
                font_filename: $font_path,
                text: trim("Eu irei!")
            );
            /**/
            //header('Content-Type: image/png');
            //imagepng($dest);

            //imagedestroy($dest);
            //imagedestroy($foto);

            //$fullpath = $path . $filename;
            //$file = new \CodeIgniter\Files\File($fullpath, true);
            $binary = imagepng($dest);//readfile($fullpath);
            return $this->response
                ->setHeader('Content-Type', 'image/png') // $file->getMimeType())
                //->setHeader('Content-disposition', 'inline; filename="' . $filename . '"')
                ->setStatusCode(200)
                ->setBody($binary);

        }

    }



    public function index1()
    {


        $profile_pic = "https://manychat.com/ava/802879/470469428/016a5cd1eda25fda4a9ff9ca4df1ac0f";
        $ig_username = "TESTE";
        $nome = $ig_username; //$_GET["name"];


        $config = new App();
        $path = ROOTPATH . $config->pathUploadFile;

        $bg = $path . 'aviva-1.png';
        $bg2 = $path . 'aviva-2.png';
        $bg3 = $path . 'aviva-3.png';
        $mf = $path . 'moldura-foto.png';

        $fg = $path . 'fotos/' . $nome . '.jpg';

        $imageurl = $profile_pic; //$_GET['url'];
        $image = imagecreatefromjpeg($imageurl);
        imagejpeg($image, $fg);

        /***********************************/
        // Obtém novos tamanhos
        list($width, $height) = getimagesize($fg);
        $newwidth = intval($width * 1.5);
        $newheight = intval($height * 1.5);

        // Carrega
        $foto = imagecreatetruecolor($newwidth, $newheight);

        // Redimensiona
        imagecopyresized($foto, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        /***********************************/
        imagedestroy($image);



        if (file_exists($bg) && file_exists($fg)) {
            // Create image instances
            $dest = imagecreatefrompng($bg);
            $wdD = imageSX($dest);
            $htD = imageSY($dest);

            //$foto = imagecreatefromjpeg($foto);
            $dest2 = imagecreatefrompng($bg2);
            $dest3 = imagecreatefrompng($bg3);
            $moldura = imagecreatefrompng($mf);

            $wd = imageSX($foto);
            $ht = imageSY($foto);

            //Tamanho do fundo
            $top = intval((imageSY($dest) - $ht) / 2) - 60;
            $left = intval((imageSX($dest) - $wd) / 2);


            // monta ordem dos layers
            $this->imagecopymerge_alpha($dest, $moldura, 0, 0, 0, 0, $wdD, $htD, 100);
            $this->imagecopymerge_alpha($dest, $foto, $left, $top, 0, 0, $wd, $ht, 80);

            $this->imagecopymerge_alpha($dest, $dest2, 0, 0, 0, 0, $wdD, $htD, 100);
            $this->imagecopymerge_alpha($dest, $dest3, 0, 0, 0, 0, $wdD, $htD, 100);


            imagesavealpha($dest, true);
            // Output and free from memory


            // Add the text to the imag
            $navy = imagecolorallocate($image, 3, 5, 58);
            $font_size = 20;
            $font_path = "fonts/font.ttf";
            $font_path = "fonts/Helvetica/HelveticaNowDisplay-Regular.ttf";
            //$font = file_get_contents("http://themes.googleusercontent.com/static/fonts/abel/v3/RpUKfqNxoyNe_ka23bzQ2A.ttf");
            //file_put_contents( $font_path , $font);
            $text = $this->wrapText("@" . $nome, $font_size, $font_path);


            // This is our cordinates for X and Y para o texto (centraliza o texto)
            $bbox = imageftbbox($font_size, 0, $font_path, $text);
            $leftText = $bbox[0] + (imageSX($dest) / 2) - ($bbox[4] / 2) - 5;
            $topText = $top;//- $ht;
            //$y = 50 + $bbox[1] + (imageSY($im) / 2) - ($bbox[5] / 2) - 5;

            imagettftext(
                image: $dest,
                size: $font_size,
                angle: 0,
                x: intval($leftText),
                y: intval($topText - ($font_size * 2)),
                color: $navy,
                font_filename: $font_path,
                text: trim($text)
            );

            imagettftext(
                image: $dest,
                size: $font_size + 10,
                angle: 0,
                x: intval($leftText),
                y: intval($topText - ($font_size * 4)),
                color: $navy,
                font_filename: $font_path,
                text: trim("Eu irei!")
            );
            /**/
            header('Content-Type: image/png');
            imagepng($dest);
            //echo $dest;
            /*     imagedestroy($dest);
                 imagedestroy($foto);

                 $fullpath = $path . $filename;
                 $file = new \CodeIgniter\Files\File($fullpath, true);
                 $binary = readfile($fullpath);
                 return $this->response
                     ->setHeader('Content-Type', $file->getMimeType())
                     ->setHeader('Content-disposition', 'inline; filename="' . $filename . '"')
                     ->setStatusCode(200)
                     ->setBody($binary);
 */
        }

    }

    function wrapText(string $text, int $font_size, string $font_path): string
    {
        // A variable to store our result in
        $wrapped = '';

        // Split the text into an array of words
        $words = explode(' ', $text);

        foreach ($words as $word) {
            // Calculate the size of the current result + the additional word
            $teststring = "{$wrapped} {$word}";
            $testbox = imagettfbbox($font_size, 0, $font_path, $teststring);

            // If the test box width is larger than our max allowed width,
            // add a line break before the word, otherwise add a space
            if ($testbox[2] > 1900) {
                $wrapped .= "\n" . $word;
            } else {
                $wrapped .= ' ' . $word;
            }
        }

        return $wrapped;
    }

    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);

        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }
}
