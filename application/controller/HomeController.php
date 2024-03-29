<?php

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Home extends Controller
{
    function __construct(){
        require APP . 'core/model.php';
        $this->model = new Model();
    }
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/home/index (which is the default page btw)
     */
    public function index()
    {
        $about = file(APP . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'OldGibberish' . DIRECTORY_SEPARATOR . 'txt' . DIRECTORY_SEPARATOR . 'about');
        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/_templates/navigation.php';
        require APP . 'view/home/index.php';
        require APP . 'view/_templates/footer.php';
    }
}
