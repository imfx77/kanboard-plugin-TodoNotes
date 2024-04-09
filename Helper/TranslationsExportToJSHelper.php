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
    private $translationsExported = false;

    /**
     * Exports a hidden textarea element which content is
     * a JSON representation of (key, text) translations for the requested textIds
     * Also includes the required script to handle them
     * @param  array   $textIds     Array
     */
    public function export(array $textIds)
    {
        // early return, avoid duplicating translations and JS
        if ($this->translationsExported) {
            return;
        }

        $translations = array();
        foreach ($textIds as $textId) {
            $translations[$textId] = t($textId);
        }

        echo $this->helper->asset->js('plugins/BoardNotes/Assets/js/translations.js');
        echo '<textarea id="_BoardNotes_TranslationsExportToJS_" style="display: none">';
        echo json_encode($translations);
        echo '</textarea>';

        $this->translationsExported = true;
    }
}
