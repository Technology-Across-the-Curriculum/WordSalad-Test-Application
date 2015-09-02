<?php
    class TestController extends Controller
    {
        function __construct()
        {
            require APP . 'model/TestModel.php';
            $this->model = new TestModel();
        }

        public function index()
        {
            // load views
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/test/index.php';
            require APP . 'view/_templates/footer.php';
        }

        public function databaseTest()
        {
            $result = $this->model->DatabaseData();
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/test/testoutcome.php';
            require APP . 'view/_templates/footer.php';
        }
    }