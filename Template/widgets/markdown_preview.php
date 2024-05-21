<?php

if (!empty($markdown_text)) {
    print $this->helper->text->markdown($markdown_text);
} else {
    print t('TodoNotes__PROJECT_NOTE_DETAILS_EDIT_HINT');
}
