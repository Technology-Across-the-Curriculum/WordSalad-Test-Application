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
            $threshold = $this->model->GetThreshold();
            $allTest = $this->model->GetAllTest();
            // load views
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/navigation.php';
            require APP . 'view/test/index.php';
            require APP . 'view/_templates/footer.php';
        }
        public function databaseTest()
        {
            $result = $this->model->DatabaseData();
            if($result != false){
                header('location: ' . URL . 'TestController/displayScores');
            }
            else{
                header('location: ' . URL . 'TestController/index');
            }

        }
        public function displayScores()
        {
            $currentSet = $this->model->GetCurrentTest();
            if ($currentSet != false) {
                $results = $this->model->GetScores();
                if ($results != null) {
                    $threshold = $this->model->GetThreshold();
                    require APP . 'view/_templates/header.php';
                    require APP . 'view/_templates/navigation.php';
                    require APP . 'view/test/dispalyscores.php';
                    require APP . 'view/_templates/footer.php';
                }
            }
            else {
                header('location: ' . URL . 'TestController/index');
            }
        }
        public function testScores($test_id){
            if(isset($test_id)){
                $results = $this->model->GetScores($test_id);
                if(isset($results))
                {
                    $threshold = $this->model->GetThreshold($test_id);
                    require APP . 'view/_templates/header.php';
                    require APP . 'view/_templates/navigation.php';
                    require APP . 'view/test/dispalyscores.php';
                    require APP . 'view/_templates/footer.php';
                }
            }
            else{
                header('location: ' . URL . 'TestController/index');
            }
        }
    }