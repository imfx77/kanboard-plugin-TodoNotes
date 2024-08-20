<?php

/**
 * Class TranslationsExportToJSHelper
 * @package Kanboard\Plugin\TodoNotes\Helper
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Helper;

use Kanboard\Core\Base;

class TranslationsExportToJSHelper extends Base
{
    private $translationsExported = false;

    /**
     * Exports a hidden textarea element which content is
     * a JSON representation of (key, text) translations for the requested textIds
     * Also includes the required script to handle them
     * @param  array   $textIds     Array
     */
    public function export(array $textIds): string
    {
        // early return, avoid duplicating translations and JS
        if ($this->translationsExported) {
            return '';
        }

        $translations = array();
        foreach ($textIds as $textId) {
            $translations[$textId] = t($textId);
        }

        $exportTranslationsToJS = json_encode($translations);

        echo $this->helper->asset->js('plugins/TodoNotes/Assets/js/translations.js');
        echo '<textarea id="_TodoNotes_TranslationsExportToJS_" style="display: none">';
        echo $exportTranslationsToJS;
        echo '</textarea>';

        $this->translationsExported = true;

        return $exportTranslationsToJS;
    }
}
