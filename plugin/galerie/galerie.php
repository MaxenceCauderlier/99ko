<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

## Fonction d'installation

function galerieInstall() {
    if (!file_exists(DATA_PLUGIN . 'galerie/galerie.json')) {
        @mkdir(UPLOAD . 'galerie/');
        @chmod(UPLOAD . 'galerie', 0755);
        $data = array();
        util::writeJsonFile(DATA_PLUGIN . 'galerie/galerie.json', $data);
    }
}

## Hooks

function galerieEndFrontHead() {
    
}

function galerieGenerateEditor() {
    global $runPlugin;
    $introEditor = new Editor('introduction', $runPlugin->getConfigVal('introduction'), lang::get('galerie.introduction'), true);
    return $introEditor;
}

## Code relatif au plugin

class galerie {

    private $items;
    private $size;

    public function __construct() {
        $data = array();
        if (file_exists(DATA_PLUGIN . 'galerie/galerie.json')) {
            $temp = util::readJsonFile(DATA_PLUGIN . 'galerie/galerie.json');
            if (pluginsManager::getPluginConfVal('galerie', 'order') == 'byDate')
                $temp = util::sort2DimArray($temp, 'date', 'desc');
            elseif (pluginsManager::getPluginConfVal('galerie', 'order') == 'byName')
                $temp = util::sort2DimArray($temp, 'title', 'asc');
            elseif (pluginsManager::getPluginConfVal('galerie', 'order') == 'natural')
                $temp = util::sort2DimArray($temp, 'id', 'asc');
            foreach ($temp as $k => $v) {
                $data[] = new galerieItem($v);
            }
        }
        $this->items = $data;
        $this->size = pluginsManager::getPluginConfVal('galerie', 'size');
    }

    public function getItems() {
        return $this->items;
    }

    public function createItem($id) {
        foreach ($this->items as $obj) {
            if ($obj->getId() == $id)
                return $obj;
        }
        return false;
    }

    public function saveItem($obj) {
        $id = $obj->getId();
        if ($id == '') {
            
            $obj->setId(uniqid());
            $upload = util::uploadFile('file', UPLOAD . 'galerie/', $obj->getId(), ['extensions' => ["gif", "png", "jpg", "jpeg"]]);
            if ($upload == 'success') {
                $ext = "." . util::getFileExtension($_FILES['file']['name']);
                $obj->setImg($obj->getId() . $ext);
                galerieResize(UPLOAD . 'galerie/' . $obj->getId() . $ext, '', $this->size, 100);
            }
            $this->items[] = $obj;
        } else {
            foreach ($this->items as $k => $v) {
                if ($id == $v->getId()) {
                    $upload = util::uploadFile('file', UPLOAD . 'galerie/', $obj->getId(), [['extensions' => ["gif", "png", "jpg", "jpeg"]]]);
                    if ($upload == 'success') {
                        $ext = "." . util::getFileExtension($_FILES['file']['name']);
                        $obj->setImg($obj->getId() . $ext);
                        galerieResize(UPLOAD . 'galerie/' . $obj->getId() . $ext, '', $this->size, 100);
                    }
                    $this->items[$k] = $obj;
                }
            }
        }
        return $this->saveItems();
    }

    public function delItem($obj) {
        foreach ($this->items as $k => $v) {
            if ($obj->getId() == $v->getId()) {
                unset($this->items[$k]);
            }
        }
        return $this->saveItems();
    }

    public function listCategories($hiddenItems = true) {
        $data = array();
        foreach ($this->items as $k => $v)
            if ($v->getCategory() != null && $v->getCategory() != '') {
                if ($hiddenItems || (!$hiddenItems && !$v->getHidden()))
                    $data[] = $v->getCategory();
            }
        asort($data);
        return array_unique($data);
    }

    public function useCategories() {
        if (count($this->listCategories()) > 0)
            return true;
        else
            return false;
    }

    public function getLastId() {
        $ids = array();
        foreach ($this->items as $k => $v) {
            $ids[] = $v->getId();
        }
        return max($ids);
    }

    public function countItems() {
        $nb = 0;
        foreach ($this->getItems() as $k => $obj)
            if (!$obj->getHidden())
                $nb++;
        return $nb;
    }

    private function saveItems() {
        $data = array();
        foreach ($this->items as $k => $v) {
            $data[] = array(
                'id' => $v->getId(),
                'title' => $v->getTitle(),
                'content' => $v->getContent(),
                'date' => $v->getDate(),
                'img' => $v->getImg(),
                'category' => $v->getCategory(),
                'hidden' => $v->getHidden(),
            );
        }
        if (util::writeJsonFile(DATA_PLUGIN . 'galerie/galerie.json', $data)) {
            return true;
        }
        return false;
    }

    public static function searchByfileName($name) {
        if ($name != '') {
            $galerie = new galerie();
            foreach ($galerie->getItems() as $k => $v) {
                if ($v->getImg() == $name)
                    return true;
            }
        }
        return false;
    }

}

class galerieItem {

    private $id;
    private $title;
    private $date;
    private $content;
    private $img;
    private $category;
    private $hidden;

    public function __construct($data = array()) {
        if (count($data) > 0) {
            $this->id = $data['id'] ?? '';
            $this->title = $data['title'] ?? '';
            $this->content = $data['content'] ?? '';
            $this->date = $data['date'] ?? date('Y-m-d');
            $this->img = $data['img'] ?? '';
            $this->category = $data['category'] ?? '';
            $this->hidden = $data['hidden'] ?? 0;
        }
    }

    public function setId($val) {
        $this->id = $val;
    }

    public function setTitle($val) {
        $val = trim($val);
        $this->title = $val;
    }

    public function setContent($val) {
        $this->content = trim($val);
    }

    public function setDate($val) {
        $val = trim($val);
        if ($val == '')
            $val = date('Y-m-d');
        $this->date = $val;
    }

    public function setImg($val) {
        $this->img = trim($val);
    }

    public function setCategory($val) {
        $this->category = trim($val);
    }

    public function setHidden($val) {
        $this->hidden = trim($val);
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getDate($short = false) {
        if ($short) {
            return substr($this->date, 0, 10);
        }
        return $this->date;
    }

    public function getImg() {
        return $this->img;
    }

    public function getUrl():string {
        return util::urlBuild(UPLOAD . 'galerie/' . $this->img);
    }

    public function getCategory() {
        return $this->category;
    }

    public function getHidden() {
        return $this->hidden;
    }

}

/**
 * Fonction qui permet de redimensionner une image en conservant les proportions
 * @author Frederic Gerard <https://www.frederic-gerard.com/scripts/script-php-pour-redimentionner-une-image-en-conservant-les-proportions.html>
 * @param  string  $image_path Chemin de l'image
 * @param  string  $image_dest Chemin de destination de l'image redimentionnée (si vide remplace l'image envoyée)
 * @param  integer $max_size   Taille maximale en pixels
 * @param  integer $qualite    Qualité de l'image entre 0 et 100
 * @param  string  $type       'auto' => prend le coté le plus grand
 *                             'width' => prend la largeur en référence
 *                             'height' => prend la hauteur en référence
 * @param  boolean $upload 	   true si c'est une image uploadée, false si c'est le chemin d'une image déjà sur le serveur
 * @return string              'success' => redimentionnement effectué avec succès
 *                             'wrong_path' => le chemin du fichier est incorrect
 *                             'no_img' => le fichier n'est pas une image
 *                             'resize_error' => le redimensionnement a échoué
 */

function galerieResize($image_path,$image_dest,$max_size = 800,$qualite = 100,$type = 'auto',$upload = false){

  // Vérification que le fichier existe
  if(!file_exists($image_path)):
    return 'wrong_path';
  endif;

  if($image_dest == ""):
    $image_dest = $image_path;
  endif;
  // Extensions et mimes autorisés
  $extensions = array('jpg','jpeg','png','gif');
  $mimes = array('image/jpeg','image/gif','image/png');

  // Récupération de l'extension de l'image
  $tab_ext = explode('.', $image_path);
  $extension  = strtolower($tab_ext[count($tab_ext)-1]);

  // Récupération des informations de l'image
  $image_data = getimagesize($image_path);

  // Si c'est une image envoyé alors son extension est .tmp et on doit d'abord la copier avant de la redimentionner
  if($upload && in_array($image_data['mime'],$mimes)):
    copy($image_path,$image_dest);
    $image_path = $image_dest;

    $tab_ext = explode('.', $image_path);
    $extension  = strtolower($tab_ext[count($tab_ext)-1]);
  endif;

  // Test si l'extension est autorisée
  if (in_array($extension,$extensions) && in_array($image_data['mime'],$mimes)):
    
    // On stocke les dimensions dans des variables
    $img_width = $image_data[0];
    $img_height = $image_data[1];

    // On vérifie quel coté est le plus grand
    if($img_width >= $img_height && $type != "height"):

      // Calcul des nouvelles dimensions à partir de la largeur
      if($max_size >= $img_width):
        return 'no_need_to_resize';
      endif;

      $new_width = $max_size;
      $reduction = ( ($new_width * 100) / $img_width );
      $new_height = round(( ($img_height * $reduction )/100 ),0);

    else:

      // Calcul des nouvelles dimensions à partir de la hauteur
      if($max_size >= $img_height):
        return 'no_need_to_resize';
      endif;

      $new_height = $max_size;
      $reduction = ( ($new_height * 100) / $img_height );
      $new_width = round(( ($img_width * $reduction )/100 ),0);

    endif;

    // Création de la ressource pour la nouvelle image
    $dest = imagecreatetruecolor($new_width, $new_height);

    // En fonction de l'extension on prépare l'iamge
    switch($extension){
      case 'jpg':
      case 'jpeg':
        $src = imagecreatefromjpeg($image_path); // Pour les jpg et jpeg
      break;

      case 'png':
        $src = imagecreatefrompng($image_path); // Pour les png
      break;

      case 'gif':
        $src = imagecreatefromgif($image_path); // Pour les gif
      break;
    }

    // Création de l'image redimentionnée
    if(imagecopyresampled($dest, $src, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height)):

      // On remplace l'image en fonction de l'extension
      switch($extension){
        case 'jpg':
        case 'jpeg':
          imagejpeg($dest , $image_dest, $qualite); // Pour les jpg et jpeg
        break;

        case 'png':
          $black = imagecolorallocate($dest, 0, 0, 0);
          imagecolortransparent($dest, $black);

          $compression = round((100 - $qualite) / 10,0);
          imagepng($dest , $image_dest, $compression); // Pour les png
        break;

        case 'gif':
          imagegif($dest , $image_dest); // Pour les gif
        break;
      }

      return 'success';
      
    else:
      return 'resize_error';
    endif;

  else:
    return 'no_img';
  endif;
}

