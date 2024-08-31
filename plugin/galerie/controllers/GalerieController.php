<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class GalerieController extends PublicController
{

    public function home()
    {
        $galerie = new galerie();
        $this->runPlugin->setTitleTag($this->runPlugin->getConfigVal('label'));
        if($this->runPlugin->getIsDefaultPlugin()){
            $this->runPlugin->setTitleTag($this->core->getConfigVal('siteName'));
            $this->runPlugin->setMetaDescriptionTag($this->core->getConfigVal('siteDescription'));
        }
        $this->runPlugin->setMainTitle($this->runPlugin->getConfigVal('label'));

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('galerie', 'galerie');

        $tpl->set('galerie', $galerie);

        $response->addTemplate($tpl);
        return $response;
    }
}