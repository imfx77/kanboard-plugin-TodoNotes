<?php

/**
 * Class TodoNotesModelTest
 * @package Kanboard\Plugin\TodoNotes\Test\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Test\Model;

require_once __DIR__ . '/../../../../tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\TodoNotes\Model\TodoNotesModel;

class TodoNotesModelTest extends \Base
{
    protected $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $loader = new Loader($this->container);
        $loader->scan();
    }

    public function testChecks()
    {
        $model = new TodoNotesModel($this->container);
        $this->assertNotTrue($model->IsUniqueNote(0, 0, 0));
        $this->assertNotTrue($model->IsCustomGlobalProject(0));
    }

    public function testReindex()
    {
        $model = new TodoNotesModel($this->container);
        $this->assertTrue($model->ReindexNotesAndLists(false));
    }
}
