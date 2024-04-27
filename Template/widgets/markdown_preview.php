<?php

    if (!empty($markdown_text)) {
        print $this->helper->text->markdown($markdown_text);
    } else {
        print t('BoardNotes_PROJECT_NOTE_DETAILS_EDIT_HINT');
    }
