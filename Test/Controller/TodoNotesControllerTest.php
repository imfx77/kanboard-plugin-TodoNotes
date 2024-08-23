<?php

/**
 * Class TodoNotesControllerTest
 * @package Kanboard\Plugin\TodoNotes\Test\Controller
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Test\Controller;

require_once __DIR__ . '/../../../../tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\TodoNotes\Controller\TodoNotesController;

class TodoNotesControllerTest extends \Base
{
    protected $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $loader = new Loader($this->container);
        $loader->scan();
    }

    public function testReindex()
    {
        $controller = new TodoNotesController($this->container);
        $this->assertEquals($controller->ReindexNotesAndLists(), true);
    }
}
