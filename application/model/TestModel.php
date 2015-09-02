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

        /**
         * Added a new test to ws_test
         */
        private function SetTestInfo(){
            $sql = "INSERT INTO ws_test(threshold) VALUES (:threshold)";
            $query = $this->db->prepare($sql);
            $threshold = $this->WordSalad->GetAverage();
            $parameters = array(':threshold' => $threshold);
            $query->execute($parameters);
        }
        private function SetScore($id,$score)
        {
            $isGibberish = null;
            $threshold = $this->CurrentTest->threshold;
            $sql = "INSERT INTO ws_score(ws_test_id,ws_gibberish_id,gibberish_score,is_gibberish) VALUE (:test_id, :gibberish_id, :score, :is_gibberish)";
            $query = $this->db->prepare($sql);
            if($score >= $threshold){
                $isGibberish = true;
            }
            else{
                $isGibberish = false;
            }
            $parameters = array(':test_id' =>$this->CurrentTest->id,':gibberish_id' => $id, ':score' => $score, 'is_gibberish' =>$isGibberish);
            $query->execute($parameters);

        }

        /**
         * Retrieves the last test added to the database
         * true for found
         * false for not found
         * @return bool

         */
        private function GetCurrentTest(){
            $sql = "SELECT id , test_time, threshold FROM ws_test ORDER BY id DESC LIMIT 1";
            $query = $this->db->prepare($sql);
            $query->execute();
            $test = $query->fetchAll();
            $this->CurrentTest = $test[0];
            if($this->CurrentTest != null)
            {
                return true;
            }
            return false;
        }

        /**
         * Test control data to make sure Gibberish Detector is working
         * @return Array() $resultArray
         */
        public function TestControlData()
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
        public function DatabaseData()
        {
            self::SetTestInfo();
            if(self::GetCurrentTest() != false) {
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

                return $resultArray;
            }
            return null;
        }
    }