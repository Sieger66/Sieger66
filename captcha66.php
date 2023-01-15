<?php

#http://rodomontano.altervista.org/captcha.php
define('_JEXEC', 1);
$PFAD = dirname(__DIR__ , 3);
define('JPATH_BASE', $PFAD );
use Joomla\CMS\Factory;
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';
// Boot the DI container
 $container = \Joomla\CMS\Factory::getContainer();


/*
* Alias the session service keys to the web session service as that is the primary session backend for this application
*
* In addition to aliasing "common" service keys, we also create aliases for the PHP classes to ensure autowiring objects
* is supported.  This includes aliases for aliased class names, and the keys for aliased class names should be considered
* deprecated to be removed when the class name alias is removed as well.
*/
 $container->alias('session.web', 'session.web.site')
->alias('session', 'session.web.site')
->alias('JSession', 'session.web.site')
->alias(\Joomla\CMS\Session\Session::class, 'session.web.site')
->alias(\Joomla\Session\Session::class, 'session.web.site')
->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

// Instantiate the application.
$app = $container->get(\Joomla\CMS\Application\SiteApplication::class);

// Set the application as global app
\Joomla\CMS\Factory::$application = $app;





header('Content-Type: image/gif');                                                                                                                                                  
header('Cache-control: no-cache, no-store'); 

# Configurazione parametri

// session_start();
$height = 32;                                                                                                                                                                   
/* height = Altezza dell'immagine in pixel. La larghezza ed il numero di frames
   vengono calcolati automaticamente. Immagini più grandi richiedono
   un maggior numero di frames affinchè l'animazione risulti fluida e
   sono quindi più pesanti
   */
   
   
$delay = 12;
/* delay  = il tempo di visualizzazione di ogni frame in centesimi di secondo.
    4 centesimi di secondo significa 25 fps (frames per secondo) Valori elevati
    rendono l'animazione più lenta. Firefox è in grado di visualizzare correttamente
    25 fps, mentre Internet Explorer non è comunque in grado di visualizzare
    più di 12 fps corrispondente ad un delay = 8. Per immagini piccole è meglio
    non usare delay troppo bassi
   */

$font = 'sans.ttf';
/* font  = il carattere TTF può essere cambiato, ma potrebbe non essere visualizzato
    in modo perfettanmente centrato
   */

$ttf = 1;
/* ttf  = 1 viene  usato il carattere TTF
   ttf  = 0 non viene usato il carattere TTF e l'immagine ha dimesioni fisse 16 x 100 pixel
   */
   
$anim_mode = 3;
/* anim_mode  = modalità dell'animazione
  0 bordi laterali oscillanti
  1 sfere rotolanti
  2 somma scorrevole
  3 animazione casuale - ogni volta che la pagina viene caricata il tipo di animazione
    cambia casualmente tra le tre disponibili
   */

# Non cambiare nulla qui sotto se non sai quello che fai

############################################################

if ($anim_mode == 3) {$anim_mode = rand(0,2);}

//  Determino la dimensione del carattere in base all'altezza dell'immagine
$fontsize = $height * 0.7;	
$tot_frames = $height;
// $somma =  $_SESSION['somma'] ;
// $somma2 = $_SESSION['somma2'];


 $session = Factory::getSession();
 $sessionD = $session->get('captcha', array(), 'S66Captcha\S66Captcha');
 $somma =    $session->get('somma', array(), 'S66Captcha\S66Captcha');
 $somma2 =   $session->get('somma2', array(), 'S66Captcha\S66Captcha');



// Determino la larghezza dell'immagine
$textbox = imagettfbbox($fontsize , 0 , $font , $somma) or die('Error in imagettfbbox function');


if ($ttf == 0)
 { 
 $width = 100;
 $height = 16;
 $delay = 12;
 if ($anim_mode == 2)

 { $width = 80;
    }
    

    }
    
    else {
    
    if ($anim_mode < 2)

 { $width = (abs($textbox[4] - $textbox[0]))*1.1;
    }
    else {
    $width = (abs($textbox[4] - $textbox[0]))*0.8;
    }
    
    }

$x = $width*0.035;
$y = $height - $height/4;


$anim_len = 10;
$start_dummy = rand(0,10);                                                                                                                                                          
$end_dummy = rand($start_dummy+$anim_len,30); 



$files = array(); 

function get_gif_header($gif_data) {                                                                                                                                                
    $header = array();                                                                                                                                                              
    $header["signature"] = substr($gif_data,0,3);                                                                                                                                  
    $header["version"]   = substr($gif_data,3,3);                                                                                                                                  
    $header["logical_screen_width"]  = substr($gif_data,6,2);                                                                                                                      
    $header["logical_screen_height"] = substr($gif_data,8,2);                                                                                                                      
    $header["packed"] = substr($gif_data,10,1);                                                                                                                                    
    $header["background_color_index"] = substr($gif_data,11,1);                                                                                                                    
    $header["pixel_aspect_ratio"] = substr($gif_data,12,1);                                                                                                                        
    $packed = ord($header["packed"]);                                                                                                                                              
    if (($packed >> 7) & 0x1) {                                                                                                                                                    
        $gct = $packed & 3;                                                                                                                                                        
        $gct_size = 3 * pow(2,$gct+1);                                                                                                                                              
        $header["global_color_table"] = substr($gif_data,13,$gct_size);                                                                                                            
    }                                                                                                                                                                              
    return $header;                                                                                                                                                                
}        

function strip_gif_header($gif_data) {                                                                                                                                              
    $without_header = "";                                                                                                                                                          
    $header_len = 0;                                                                                                                                                                
    $header = get_gif_header($gif_data);                                                                                                                                            
    foreach ($header as $k=>$v)                                                                                                                                                    
        $header_len += strlen($v);                                                                                                                                                  
    return substr($gif_data,$header_len,strlen($gif_data)-$header_len);                                                                                                            
}        

function get_gif_image_data($gif_data) {                                                                                                                                            
    $no_header = strip_gif_header($gif_data);                                                                                                                                      
    $no_header = substr($no_header,0,strlen($no_header)-1);                                                                                                                        
    return $no_header;                                                                                                                                                              
}      
function get_gif_image_descriptor($image_data) {                                                                                                                                    
    $header = array();                                                                                                                                                              
    $header["image_separator"] = substr($image_data,0,1);                                                                                                                          
    $header["image_left_position"]  = substr($image_data,1,2);                                                                                                                      
    $header["image_top_position"] = substr($image_data,3,2);                                                                                                                        
    $header["image_width"]  = substr($image_data,5,2);                                                                                                                              
    $header["image_height"] = substr($image_data,7,2);                                                                                                                              
    $header["packed"] = substr($image_data,9,1);                                                                                                                                    
    $packed = ord($header["packed"]);                                                                                                                                              
    if (($packed >> 7) & 0x1) {                                                                                                                                                    
        $lct = $packed & 3;                                                                                                                                                        
        $lct_size = 3 * pow(2,$lct+1);                                                                                                                                              
        $header["local_color_table"] = substr($image_data,10,$lct_size);                                                                                                            
    }                                                                                                                                                                              
    return $header;                                                                                                                                                                
}    
function strip_gif_image_descriptor($imgdata) {                                                                                                                                    
    $descriptor = get_gif_image_descriptor($imgdata);                                                                                                                              
    $len = 0;                                                                                                                                                                      
    foreach ($descriptor as $k=>$v)                                                                                                                                                
        $len += strlen($v);                                                                                                                                                        
    return substr($imgdata,$len,strlen($imgdata)-$len);                                                                                                                            
}      
function make_gifanim($gifs) {
    global $delay;                                                                                                                                                  
    $head0 = get_gif_header($gifs[0]);                                                                                                                                              
    $head0["packed"] = chr( ord($head0["packed"]) & (7 << 4) );                                                                                                                    
    $head0["background_color_index"] = chr(0);                                                                                                                                      
    $head0["pixel_aspect_ratio"] = chr(0);                                                                                                                                          
    unset($head0["global_color_table"]);                                                                                                                                            
    $anim_gif = implode("",$head0);                                                                                                                                                
    $extra_info = array( chr(0x21), chr(0xff) ,chr(0x0B), "NETSCAPE2.0",chr(0x03), chr(0x01), chr(0x00).chr(0x00), chr(0x00) );                                                    
    $anim_gif .= implode("",$extra_info);                                                                                                                                          
    foreach ($gifs as $gif) {                                                                                                                                                      
        $header = get_gif_header($gif);                                                                                                                                            
        $imgdata = get_gif_image_data($gif);                                                                                                                                        
        $image_header = get_gif_image_descriptor($imgdata);                                                                                                                        
        $image_only = strip_gif_image_descriptor($imgdata);                                                                                                                        
        $control_block = array();                                                                                                                                                  
        $control_block["extension_introducer"] = chr(0x21);                                                                                                                        
        $control_block["graphic_control_label"]  = chr(0xF9);                                                                                                                      
        $control_block["block_size"] = chr(4);                                                                                                                                      
        $control_block["packed"] = chr(0);                                                                                                                                          
        $control_block["delay"] = chr($delay).chr(0);                                                                                                                                 
        $control_block["transparent_color_index"] = chr(0);                                                                                                                        
        $control_block["terminator"] = chr(0);                                                                                                                                      
        if (!isset($image_header["local_color_table"]) && isset($header["global_color_table"])) {                                                                                  
            $image_header["local_color_table"] = $header["global_color_table"];                                                                                                    
            $size_gct = (ord($header["packed"]) & 3);                                                                                                                              
            $image_header["packed"] = chr( ord($image_header["packed"]) | (0x1 << 7) | ($size_gct) );                                                                              
        }                                                                                                                                                                          
        $anim_gif .= implode("",$control_block).implode("",$image_header).$image_only;                                                                                              
    }                                                                                                                                                                              
    $anim_gif .= chr(0);                                                                                                                                                            
    return $anim_gif;                                                                                                                                                              
}  

$cur2_x = $tot_frames*2;

for ($f=0;$f<$tot_frames;$f++) {  
    $im = imagecreate($width, $height)                                                                                                                                                                                                                                                                                    
        or die("Cannot Initialize new GD image stream");  
        
    // Creo alcuni colori
$bianco = imagecolorallocate($im, 255, 255, 255);
$grigio = imagecolorallocate($im, 238, 238, 238);
$grigio2 = imagecolorallocate($im, 200, 200, 200);
$nero = imagecolorallocate($im, 0, 0, 0);
$rosso = imagecolorallocate($im, 255, 0, 0);
$verde = imagecolorallocate($im, 0, 255, 0);
$blu = imagecolorallocate($im, 0, 0, 255);
$azzurro = imagecolorallocate($im, 168, 223, 244);


   
   /*definisco i colori dello sfondo, del testo e delle parti mobili
   I colori possono essere cambiati a piacere
   */
$colore_sfondo =  $rosso;
$colore_testo =  $nero;
$colore_anim1 =  $grigio;
$colore_anim2 =  $grigio;                                                                                                                                 
    
    ImageFill($im, 0, 0, $colore_sfondo);                                                                                                                                                                      
                                                                                                                                                                        
    if ($f>$start_dummy && $f<$end_dummy)                                                                                                                                          
    
    ImageFill($im, 0, 0, $colore_sfondo);
   
     
     
     
     
     if ($anim_mode == 0) {
     
     if ($ttf == 1)
 { ImageTtfText($im,$fontsize,0,$x,$y,$colore_testo,$font,$somma);
    }
    else {
    ImageString($im, 4, 3, 1, $somma, $nero);
    }
      
      
      if ($cur_x<$tot_frames){
     imagefilledrectangle($im,-2,0,$cur_x-2,$height,$colore_anim1);
    imagefilledrectangle($im,$cur_x+($width-$tot_frames+1),0,$width+2,$width,$colore_anim1);
    }
    else {
    imagefilledrectangle($im,-2,0,$cur2_x-2,$height,$colore_anim1);
    imagefilledrectangle($im,$cur2_x+($width-$tot_frames+1),0,$width+2,$width,$colore_anim1);
    }
    
     
    
    $cur_x = $cur_x + 2;                                                                                                                                                                              
    $cur2_x = $cur2_x - 2;
     
		 
} else {  if ($anim_mode == 1) {

if ($ttf == 1)
 { ImageTtfText($im,$fontsize,0,$x,$y,$colore_testo,$font,$somma);
    }
    else {
    ImageString($im, 4, 3, 1, $somma, $nero);
    }
    
imagefilledellipse($im, $cur3_x, $height/2, $height, $height, $colore_anim2);
    imagefilledellipse($im, $width/2+$cur3_x, $height/2, $height, $height, $colore_anim2);
    imagefilledellipse($im, $width+$cur3_x, $height/2, $height, $height, $colore_anim2);
    imagefilledellipse($im, -$width/2+$cur3_x, $height/2, $height, $height, $colore_anim2);  
        
    $cur3_x = $cur3_x + ($width/$height); 


}	else {

if ($ttf == 1)
 { ImageTtfText($im,$fontsize,0,$x-$cur4_x,$y,$colore_testo,$font,$somma2);
    }
    else {
    ImageString($im, 4, $x-$cur4_x, 1, $somma2, $nero);
    }
        
    $cur4_x = $cur4_x + ($width/$height)/0.64;
}		 	 
}
      
    
    ob_start();                                                                                                                                                                    
    imagegif($im);                                                                                                                                                                  
    $files[] = ob_get_clean();                                                                                                                                                      
    imagedestroy($im);                                                                                                                                                              
}                                                                                                                                                                                  
                                                                                                                                                                                    
echo make_gifanim($files);  


?>