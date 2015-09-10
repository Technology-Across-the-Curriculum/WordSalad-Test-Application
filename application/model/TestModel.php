<?php
    require(APP . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'model.php');

    /**
     * Created by PhpStorm.
     * User: Nathan
     * Date: 9/2/2015
     * Time: 10:47 AM
     */
    class TestModel extends Model
    {
        private $WordSalad = null;
        private $CurrentTest = null;

        function __construct()
        {
            try {
                $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
                $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                exit('Database connection could not be established.');
            }
            require APP . 'model/WordSaladModel.php';
            $this->WordSalad = new WordSalad();
        }
        /* =================================
                Setters Function
        ================================== */
        /**
         * Added a new test to ws_test table
         */
        private function SetTestInfo()
        {
            $sql = "INSERT INTO ws_test(threshold) VALUES (:threshold)";
            $query = $this->db->prepare($sql);
            $threshold = $this->WordSalad->GetAverage();
            $parameters = array(':threshold' => $threshold);
            $query->execute($parameters);
        }

        /**
         * Sets the score of a giving id(ws_gibberish.id)
         * to ws_score table.
         * @param $id
         * @param $score
         */
        private function SetScore($id, $score)
        {
            $isGibberish = null;
            $threshold = $this->CurrentTest->threshold;
            $sql = "INSERT INTO ws_score(ws_test_id,ws_gibberish_id,gibberish_score,is_gibberish) VALUE (:test_id, :gibberish_id, :score, :is_gibberish)";
            $query = $this->db->prepare($sql);
            if ($threshold <= $score) {
                $isGibberish = false;
            } else {
                $isGibberish = true;
            }
            $parameters = array(':test_id' => $this->CurrentTest->id, ':gibberish_id' => $id, ':score' => $score, 'is_gibberish' => $isGibberish);
            $query->execute($parameters);

        }

        /* =================================
                Getters Function
        ================================== */
        public function GetAllTest(){
            $sql = "SELECT * FROM ws_test";
            $query = $this->db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        }
        /**
         * Retrieves the last test added to the database
         * true for found
         * false for not found
         * @return bool
         */
        public function GetCurrentTest()
        {
            $sql = "SELECT id , test_time, threshold FROM ws_test ORDER BY id DESC LIMIT 1";
            $query = $this->db->prepare($sql);
            $query->execute();
            $test = $query->fetchAll();
            $this->CurrentTest = $test[0];
            if ($this->CurrentTest != null) {
                return true;
            }

            return false;
        }
        // TODO Will need to be change to gets the score of any test
        public function GetScores($test_id = null)
        {
            $sql = "SELECT ws_gibberish.body_text,ws_score.gibberish_score,ws_score.is_gibberish
                    FROM ws_gibberish
                    INNER JOIN ws_score
                    on ws_gibberish.id = ws_score.ws_gibberish_id
                    WHERE ws_test_id = :test_id";
            $query = $this->db->prepare($sql);
            if(isset($test_id)) {

                $parameters = array(':test_id' => $test_id);
            }
            else{
                $parameters = array(':test_id' => $this->CurrentTest->id);
            }
            $query->execute($parameters);

            return $query->fetchAll();
        }

        /**
         * Gets the Threshold hold of the passed test id.
         * @pram $test_id
         * @return float
         */
        public function GetThreshold($test_id = null){
            if(isset($test_id)){
                $sql = "SELECT threshold FROM ws_test where id = :test_id LIMIT 1";
                $query = $this->db->prepare($sql);
                $parameters = array(':test_id' => $test_id);
                $query->execute($parameters);
                $test = $query->fetchAll();
                // only returning the first test's threshold
                return $test[0]->threshold;
            }
            else{
                self::GetCurrentTest();
                return $this->CurrentTest->threshold;
            }

        }


        /* =================================
                Main Methods
        ================================== */
        /**
         * Test control data to make sure Gibberish Detector is working properly
         * @return Array() $resultArray
         */
        public function ControlData()
        {

            //Test Gibberish Data
            $textArray = array(
                'my name is rob and i like to hack',
                'is this thing working?',
                'i hope so',
                't2 chhsdfitoixcv',
                'ytjkacvzw',
                'yutthasxcvqer',
                'seems okay',
                'yay!',
                'How it works
============
The markov chain first \'trains\' or \'studies\' a few MB of English text, recording how often characters appear next to each other. Eg, given the text "Rob likes hacking" it sees Ro, ob, o[space], [space]l, ... It just counts these pairs. After it has finished reading through the training data, it normalizes the counts. Then each character has a probability distribution of 27 followup character (26 letters + space) following the given initial.',
                'So then given a string, it measures the probability of generating that string according to the summary by just multiplying out the probabilities of the adjacent pairs of characters in that string. EG, for that "Rob likes hacking" string, it would compute prob[\'r\'][\'o\'] * prob[\'o\'][\'b\'] * prob[\'b\'][\' \'] ... This probability then measures the amount of \'surprise\' assigned to this string according the data the model observed when training. If there is funny business with the input string, it will pass through some pairs with very low counts in the training phase, and hence have low probability/high surprise.',
                'To die: thought hwegqxrehqrhqt4hwetrgqferfthose to say count a cowardelay, that sleegqxrehqrhqt4hwetrgqf to othe ressor\'s their current merit of gream: ay, things of deathe wish\'d. To     lh  nwcno   wef;    wjkecldskjhfyerugqb3ruqvu8qr3upg3qgk;x3oqrgxqegqdie: that is not thought, and makesegqxrehqrhqt4hwetrgqf we know nobler to egqxrehqrhqt4hwetrgqfdeath, the of segqxrehqrhqt4hwetrgqfhims',
                'To die: thought his a weath, those to say count a cowardelay, that sleep of time, and scorns that fly to othe ressor\'s their current merit of gream: ay, xrehqrhqt4hwetthings of deathe wish\'d. To die: that ihqrhqt4hwet not thought, and makes us a we have, their current a sliegqxrehqrhqng a we know nobler to sleep of so lose to be what ishqrhqt4hwetressor\'s that is quieturns, and to say consummative have, to gruntry fromxrehqrhqt4hwet when we have hue of us fortal shocks the arrows of us and scorns of action deatxrehqrhqt4hweth, the of so lose there\'s we himself might hims',
            );
            $resultArray = Array(
                "text"  => Array(),
                "score" => Array()
            );

            foreach ($textArray as $txt) {
                array_push($resultArray['text'], $txt);
                array_push($resultArray['score'], $this->Test($txt, $this->Matrix, true));
            }

            return $resultArray;
        }

        /**
         * Test our Sample Text with the Gibberish Detector
         * Records gibberish score of each Text entry
         * return true if all scores where recorded and method worked properly
         * return false if something failed or went wrong
         * @return bool
         */
        public function DatabaseData()
        {
            self::SetTestInfo();
            if (self::GetCurrentTest() != false) {
                $sql = "SELECT * FROM ws_gibberish";
                $query = $this->db->prepare($sql);
                $query->execute();
                $results = $query->fetchAll();

                $resultArray = Array(
                    "text"  => Array(),
                    "score" => Array()
                );
                foreach ($results as $row) {
                    $score = $this->WordSalad->Test($row->body_text, $this->WordSalad->GetMatrix(), true);
                    array_push($resultArray['text'], $row->body_text);
                    array_push($resultArray['score'], $score);
                    self::SetScore($row->id, $score);
                    unset($score);
                }
                $testData = self::GetScores($this->CurrentTest->id);
                if ($testData != null) {
                    return true;
                }
            }
            return false;
        }

        public function DeleteTest($test_id){
            $sql = "DELETE FROM ws_test WHERE id = :test_id";
            $query = $this->db->prepare($sql);
            $parameters = array(':test_id' => $test_id);
            $query->execute($parameters);
        }
    }