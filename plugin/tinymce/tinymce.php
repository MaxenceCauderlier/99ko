<?php

/**
 * @copyright (C) 2024, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
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

function tinymceInstall() {
    
}

## Hooks

function tinymceAdminHead() {
    $uploadUrl = router::getInstance()->generate('filemanager-upload-api', ['token' => UsersManager::getCurrentUser()->token]);
    $url = util::urlBuild(PLUGINS . 'tinymce/lib/tinymce/tinymce.min.js');
    $options = "language: '". lang::getLocale(). "',
        images_upload_url: '" . $uploadUrl . "',
        automatic_uploads: true,
        convert_urls: false,
        document_base_url : '" . core::getInstance()->getConfigVal('siteUrl') . "/',
        browser_spellcheck: true,
        plugins: 'advlist anchor autolink autoresize charmap code codesample emoticons fullscreen help image insertdatetime link lists media nonbreaking searchreplace table visualblocks visualchars wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor removeformat | insertfile image media link | charmap emoticons codesample | fullscreen',
        max_height: 600,
        image_caption: true,
		codesample_languages: [
    { text: 'HTML', value: 'HTML' },
	{ text: 'XML', value: 'XML' },
	{ text: 'Tpl', value: 'twig' },
    { text: 'JavaScript', value: 'javascript' },
    { text: 'CSS', value: 'css' },
    { text: 'PHP', value: 'php' },
    { text: 'Ruby', value: 'ruby' },
    { text: 'Python', value: 'python' },
    { text: 'Java', value: 'java' },
    { text: 'C', value: 'c' },
    { text: 'C#', value: 'csharp' },
    { text: 'C++', value: 'cpp' }
  ],";

    echo "<script src='" . $url . "'></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea.editor', " . $options . "
        });

        function mceAdd(item){
            tinyMCE.execCommand('mceAddEditor', false, { id: item.id, options: {" . $options . "}})
        }

        function mceEnd(item){
            tinyMCE.execCommand('mceRemoveEditor', false, { id: item.id});
        }

        Fancybox.bind('[data-src=\'#param_panel\']', {
            on: {
                'ready' : (fancybox, eventName) => {
                    document.querySelectorAll('#param_panel textarea.editor').forEach(function (item, index) {
                        mceAdd(item);
                    });
                },
                'init' : (fancybox, eventName) => {
                    document.querySelectorAll('#param_panel textarea.editor').forEach(function (item, index) {
                        mceEnd(item);
                    });
                }
            }
        });
    });
</script>";
}

function tinymceInsertScriptBeforeEditor() {
    echo '<script>' .
    'function processInsertImgInEditor(editorId, imgUrl) {
        var ed = tinyMCE.get(editorId);
        var range = ed.selection.getRng();
        var newNode = ed.getDoc().createElement ( "img" );
        newNode.src=imgUrl;
        range.insertNode(newNode);  
    }
    </script>';
}
