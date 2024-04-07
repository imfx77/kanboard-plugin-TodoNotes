<?php

namespace Kanboard\Plugin\BoardNotes\Helper;

use Kanboard\Core\Base;

/**
 * Class TranslationsExportToJSHelper
 * @package Kanboard\Plugin\BoardNotes\Controller
 * @author  Im[F(x)]
 */
class TranslationsExportToJSHelper extends Base
{
    /**
     * Exports a JSON representation of (key, text) translations for the requested textIds
     * @param  array   $textIds     Array
     */
    public function export(array $textIds)
    {
        $translations = array();
        foreach($textIds as $textId){
            $translations[$textId] = t($textId);
        }
        return json_encode($translations);
    }
}