<h1 name="user-content-readme-top" align="center">BoardNotes plugin for Kanboard</h1>

<p align="center">
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/releases">
        <img src="https://img.shields.io/github/v/release/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=brightgreen" alt="GitHub Latest Release (by date)" title="GitHub Latest Release (by date)">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/blob/master/LICENSE" title="Read License">
        <img src="https://img.shields.io/github/license/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge" alt="kanboard-plugin-BoardNotes">
    </a>
</p>
<p align="center">
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/releases">
        <img src="https://img.shields.io/github/downloads/imfx77/kanboard-plugin-BoardNotes/total?style=for-the-badge&color=orange" alt="GitHub All Releases" title="GitHub All Downloads">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/releases">
        <img src="https://img.shields.io/github/directory-file-count/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=orange" alt="GitHub Repository File Count" title="GitHub Repository File Count">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/releases">
        <img src="https://img.shields.io/github/repo-size/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=orange" alt="GitHub Repository Size" title="GitHub Repository Size">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/releases">
        <img src="https://img.shields.io/github/languages/code-size/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=orange" alt="GitHub Code Size" title="GitHub Code Size">
    </a>
</p>
<p align="center">
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/stargazers" title="View Stargazers">
        <img src="https://img.shields.io/github/stars/imfx77/kanboard-plugin-BoardNotes?logo=github&style=for-the-badge" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/discussions">
        <img src="https://img.shields.io/github/discussions/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=blue" alt="GitHub Discussions" title="Read Discussions">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/compare">
        <img src="https://img.shields.io/github/commits-since/imfx77/kanboard-plugin-BoardNotes/latest?include_prereleases&style=for-the-badge&color=blue" alt="GitHub Commits Since Last Release" title="GitHub Commits Since Last Release">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/compare">
        <img src="https://img.shields.io/github/commit-activity/m/imfx77/kanboard-plugin-BoardNotes?style=for-the-badge&color=blue" alt="GitHub Commit Monthly Activity" title="GitHub Commit Monthly Activity">
    </a>
</p>
<p align="center">
    <a href="https://github.com/kanboard/kanboard" title="Kanboard - Kanban Project Management Software">
        <img src="https://img.shields.io/badge/Plugin%20for-kanboard-D40000?style=for-the-badge&labelColor=000000" alt="Kanboard">
    </a>
</p>

---

# -= UNDER DEVELOPMENT =-

**⚠ This README is still out of date ! ⚠**

## TODO-style Notes for Kanboard

The plugin allows to keep TODO-style notes on every KB project and as standalone lists.
The notes that may appear unsuitable for creating board tasks are totally fine on the custom TODO list.
They are easy and fast to create, change and rearrange, with convenient visual aids.
Every user can privately see and operate ONLY his own notes, even if notes of multiple users are are bound to the same project.

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Features, Issues, Todo

<details>
    <summary><strong>Features</strong></summary>

```TODO: update```

**Old Take**

- Take notes quickly. Write the note title and press ENTER to save.
- Press TAB in the new note title to show the detailed menu
- Add detailed description to new notes
- Add a category to notes. The category is the same as the projects categories. (Please see the section for bugs)
- Get pie analytic on open and done notes
- Delete all done notes
- One-click for editing notes status (open/in progress/done)
- Edit note title. Click on title, edit and press ENTER
- Press the show more button on a note to see the note details
- Edit an existing notes description. Click on the description, type, press TAB to save
- Change category on existing notes. If you want to remove the category, just choose option 2 (the blank)
- Free sorting. Move the notes around. The sorting is saved.
- Export note to task. (Please see the secton for bugs)
- Generate report for printing notes.
- Filter report on category

**New Take**

- There are custom lists available only to you, and project lists which are automatically defined by the projects you have access to. Even though, notes on a project list are per user - i.e. your notes are visible and manageable only to and by you.
- Editing of the same lists is possible from multiple devices (and users if they have access to the same project), and the lists auto update on 15sec interval. If there are clashes between local and remote changes, the locals are discarded.
- Stat counts are available for every list. those also auto update upon clicking the items as open/in progress/done. Remote change of notes status get updated every 15secs.
- Main list toolbar and each note personal toolbar provide numerous actions, including transfer of notes btw lists and creating a KB board task from note, sorting and colorizing as visuals.
- Useful keyboard shortcuts (for desktop) to create and edit notes fast and easy. Click note checkbox to change its status.  DblClick note to show/hide details. Reordering of notes using drag.
- Finally, all the above functionalities and visualizations are swiftly adapted to work on mobile devices, considering smaller screen and touch input.

</details>
<details>
    <summary><strong>Issues</strong></summary>

```TODO: update```

- Focus on description textarea when pressing TAB on new notes title is not working
- Category is saved as text in database and does not have foreing key to the projects real category table
- Category not updating in title after manually changing the category
- Analytic chart on categories not developed
- Margin bottom not added
- The only folder in the `Template` folder is `boardnotes`, and not specified out on `dashboard` etc.
- There is no description of shortcuts (ENTER and TAB key)
- Delete directly on trash button on single note - to fast?
- If note has empty title, it's not possible to change it afterwards
- Analytic is breaking when viewing all projects (js not reloading correctly)
- Exporting note to task: Swimlanes not working. Category not working.
- Div modal for "Delete all done" and "Analytic" is repeated on every reload
- Should disabled projects show on all boardnotes page?
- Functions in controller (BoardNotesController) missing variables in () - needed?
- Markups as Kanboard

</details>
<details>
    <summary><strong>Todo</strong></summary>

```TODO: update```

- Implement fault procedures (verify it is number, etc.)
- Adding possibility to attach image from mobile
- Finish exporting notes to task in specific swimlane and with category
- Update styling for a more simplicity view
- Better overview of multiple projects with tabs

</details>

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Screenshots

```TODO: update```

All features are accessible via the `Project View` and the `Dashboard View`.  
Might you excuse my custom dark theme, the cyrillic texts on my board, and the rainbow colored categories ✨😝

### (temporary pic of) Project View

![(temporary pic of) Project View](https://private-user-images.githubusercontent.com/76657062/320924221-e80af11f-4e20-4fbb-9821-7d0648faa2f2.png?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3MTI2OTEwMTcsIm5iZiI6MTcxMjY5MDcxNywicGF0aCI6Ii83NjY1NzA2Mi8zMjA5MjQyMjEtZTgwYWYxMWYtNGUyMC00ZmJiLTk4MjEtN2QwNjQ4ZmFhMmYyLnBuZz9YLUFtei1BbGdvcml0aG09QVdTNC1ITUFDLVNIQTI1NiZYLUFtei1DcmVkZW50aWFsPUFLSUFWQ09EWUxTQTUzUFFLNFpBJTJGMjAyNDA0MDklMkZ1cy1lYXN0LTElMkZzMyUyRmF3czRfcmVxdWVzdCZYLUFtei1EYXRlPTIwMjQwNDA5VDE5MjUxN1omWC1BbXotRXhwaXJlcz0zMDAmWC1BbXotU2lnbmF0dXJlPTM2ZjdkMmU1OTAxZDIzOTgxNzdhNzBiMWZkNjgyYzdhN2VmZjJlMDk4YjhmNzk5Yjk1Yzc1NDBiYzQyZGI4YWYmWC1BbXotU2lnbmVkSGVhZGVycz1ob3N0JmFjdG9yX2lkPTAma2V5X2lkPTAmcmVwb19pZD0wIn0.BqeiK8prfA6UghjZ0q_Wn_v_FG0jG45IoY1u-qHxgAY)

### (temporary pic of) Dashboard View

![(temporary pic of) Dashboard View](https://private-user-images.githubusercontent.com/76657062/320925693-06ebd609-5c77-458c-96c5-873914326d40.png?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3MTI2OTEwMTcsIm5iZiI6MTcxMjY5MDcxNywicGF0aCI6Ii83NjY1NzA2Mi8zMjA5MjU2OTMtMDZlYmQ2MDktNWM3Ny00NThjLTk2YzUtODczOTE0MzI2ZDQwLnBuZz9YLUFtei1BbGdvcml0aG09QVdTNC1ITUFDLVNIQTI1NiZYLUFtei1DcmVkZW50aWFsPUFLSUFWQ09EWUxTQTUzUFFLNFpBJTJGMjAyNDA0MDklMkZ1cy1lYXN0LTElMkZzMyUyRmF3czRfcmVxdWVzdCZYLUFtei1EYXRlPTIwMjQwNDA5VDE5MjUxN1omWC1BbXotRXhwaXJlcz0zMDAmWC1BbXotU2lnbmF0dXJlPTI5OTA0NzIwZDYwZTI5ZWEzOTZlNzRmNWRjZmY0ODhiYjJiYWNlM2EwMWViMWEwNzMwMmU3MmQ0NjJmMTVmZTMmWC1BbXotU2lnbmVkSGVhZGVycz1ob3N0JmFjdG9yX2lkPTAma2V5X2lkPTAmcmVwb19pZD0wIn0.C0NoZH7kxEcFvbUBkq_sOq3qOa0Pv7ePf-1pClFpqjU)

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Usage

```TODO: update```

Take notes on the fly. Developed to easily take project specific notes. The purpose of the notes is not to be tasks, but for keeping information - a flexible alternative to metadata.
I'm using Kanboard as projectmanagement tool for managing construction projects, where I often need to take notes regarding specific installations, during site-visits or phonemeetings.

The notes is accessible from the project dropdown, where only the project specific notes will be shown. On the dashboard there's a link in the sidebar to view all notes, the notes will be separated in tabs.

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Installation & Compatibility

<details>
    <summary><strong>Installation</strong></summary>

- Install via the **Kanboard Plugin Directory** or see [INSTALL.md](INSTALL.md)
- Read the full [**Changelog**](changelog.md "See changes") to see the latest updates

</details>
<details>
    <summary><strong>Compatibility</strong></summary>

- Requires [Kanboard](https://github.com/kanboard/kanboard "Kanboard - Kanban Project Management Software") ≥`1.2.33`
- **Other Plugins & Action Plugins**
  - _No known issues_
- **Core Files & Templates**
  - `0` Template override
  - _No database changes_

</details>
<details>
    <summary><strong>Translations</strong></summary>

- _Translation for `en_US` is the default_, currently there are no other translation packs.

</details>

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Authors & Contributors

- [Im[F(x)]](https://github.com/imfx77) - Author
- Contributors welcome _for translations_ !

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## Credits & References

- [TTJ](https://github.com/ThomasTJdev) (c) 2016-2023
- [aljawaid](https://github.com/aljawaid) (c) 2023

<p align="right">[<a href="#user-content-readme-bottom">&#8595; Bottom</a>] [<a href="#user-content-readme-top">&#8593; Top</a>]</p>

## License

- This project is distributed under the [MIT License](LICENSE "Read The MIT license")

<p align="right">[<a href="#user-content-readme-top">&#8593; Top</a>]</p>

---

<p align="center">
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/stargazers" title="View Stargazers">
        <img src="https://img.shields.io/github/stars/imfx77/kanboard-plugin-BoardNotes?logo=github&style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/forks" title="See Forks">
        <img src="https://img.shields.io/github/forks/imfx77/kanboard-plugin-BoardNotes?logo=github&style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/blob/master/LICENSE" title="Read License">
        <img src="https://img.shields.io/github/license/imfx77/kanboard-plugin-BoardNotes?style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/issues" title="Open Issues">
        <img src="https://img.shields.io/github/issues-raw/imfx77/kanboard-plugin-BoardNotes?style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/issues?q=is%3Aissue+is%3Aclosed" title="Closed Issues">
        <img src="https://img.shields.io/github/issues-closed/imfx77/kanboard-plugin-BoardNotes?style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/discussions" title="Read Discussions">
        <img src="https://img.shields.io/github/discussions/imfx77/kanboard-plugin-BoardNotes?style=flat-square" alt="kanboard-plugin-BoardNotes">
    </a>
    <a href="https://github.com/imfx77/kanboard-plugin-BoardNotes/compare/" title="Latest Commits">
        <img alt="GitHub commits since latest release (by date)" src="https://img.shields.io/github/commits-since/imfx77/kanboard-plugin-BoardNotes/latest?style=flat-square">
    </a>
</p>

<a name="user-content-readme-bottom"></a>
