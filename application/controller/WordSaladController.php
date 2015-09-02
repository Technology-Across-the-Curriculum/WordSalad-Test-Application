<?php
    class WordSaladController extends Controller
    {
        function __construct()
        {
            require APP . 'model/WordSaladModel.php';
            $this->model = new WordSalad();
        }

        public function index()
        {
            $threshold = $this->model->GetAverage();
            // load views
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/wordsalad/index.php';
            require APP . 'view/_templates/footer.php';
        }

        public function initializeWordSalad()
        {
            $this->model->train();
            header('location: ' . URL . 'WordSaladController/index');
        }
        public function controlTest(){
            $resultArray = $this->model->TestControlData();

            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/wordsalad/wordsaladscore.php';
            require APP . 'view/_templates/footer.php';
        }

        public function databaseTest(){
            $resultArray = $this->model->DatabaseData();

            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/wordsalad/wordsaladscore.php';
            require APP . 'view/_templates/footer.php';
        }
    }