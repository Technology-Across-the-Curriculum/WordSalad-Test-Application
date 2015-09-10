<?php

    /**
     * Created by PhpStorm.
     * User: Nathan
     * Date: 8/28/2015
     * Time: 4:11 PM
     */
    class WordSalad
    {
        private $Big;
        private $Good;
        private $Bad;
        private $Matrix;
        private $BigDirectory;


        function __construct()
        {
            $this->Big = APP . 'libs' . DIRECTORY_SEPARATOR . 'OldGibberish' . DIRECTORY_SEPARATOR . 'txt' . DIRECTORY_SEPARATOR . 'big.txt';
            $this->Good = APP . 'libs' . DIRECTORY_SEPARATOR . 'OldGibberish' . DIRECTORY_SEPARATOR . 'txt' . DIRECTORY_SEPARATOR . 'good.txt';
            $this->Bad = APP . 'libs' . DIRECTORY_SEPARATOR . 'OldGibberish' . DIRECTORY_SEPARATOR . 'txt' . DIRECTORY_SEPARATOR . 'bad.txt';
            $this->Matrix = APP . 'libs' . DIRECTORY_SEPARATOR . 'OldGibberish' . DIRECTORY_SEPARATOR . 'txt' . DIRECTORY_SEPARATOR . 'matrix.txt';
            $this->BigDirectory = APP . 'libs' . DIRECTORY_SEPARATOR . 'txt';
        }

        private static $_accepted_characters = 'abcdefghijklmnopqrstuvwxyz ';

        protected static function _normalise($line)
        {
//          Return only the subset of chars from accepted_chars.
//          This helps keep the  model relatively small by ignoring punctuation,
//          infrequenty symbols, etc.
            return preg_replace('/[^a-z\ ]/', '', strtolower($line));
        }

        public static function _averageTransitionProbability($line, $log_prob_matrix)
        {
//          Return the average transition prob from line through log_prob_mat.
            $log_prob = 1.0;
            $transition_ct = 0;
            $pos = array_flip(str_split(self::$_accepted_characters));
            $filtered_line = str_split(self::_normalise($line));
            $a = false;
            foreach ($filtered_line as $b) {
                if ($a !== false) {
                    $log_prob += $log_prob_matrix[$pos[$a]][$pos[$b]];
                    $transition_ct += 1;
                }
                $a = $b;
            }

            # The exponentiation translates from log probs to probs.
            return exp($log_prob / max($transition_ct, 1));
        }

        public static function Test($text, $lib_path, $raw = false)
        {
            if (file_exists($lib_path) === false) {
//                  TODO throw error?
                return -1;
            }
            $trained_library = unserialize(file_get_contents($lib_path));
            if (is_array($trained_library) === false) {
//                 TODO throw error?
                return -1;
            }

            $value = self::_averageTransitionProbability($text, $trained_library['matrix']);
            if ($raw === true) {
                return $value;
            }

            if ($value <= $trained_library['threshold']) {
                return true;
            }

            return false;
        }

        public function Train()
        {
            if (is_file($this->Big) === false || is_file($this->Good) === false || is_file($this->Bad) === false) {
                return false;
            }

            $k = strlen(self::$_accepted_characters);
            $pos = array_flip(str_split(self::$_accepted_characters));

//          Assume we have seen 10 of each character pair.  This acts as a kind of
//          prior or smoothing factor.  This way, if we see a character transition
//          live that we've never observed in the past, we won't assume the entire
//          string has 0 probability.
            $log_prob_matrix = array();
            $range = range(0, count($pos) - 1);
            foreach ($range as $index1) {
                $array = array();
                foreach ($range as $index2) {
                    $array[$index2] = 10;
                }
                $log_prob_matrix[$index1] = $array;
            }
//          Gets book from txt directory and removes '..' and '.' from the directory scan. By Nathan Healea
            $books = array_diff(scandir($this->BigDirectory), array('..', '.'));

//          Count transitions from all the books in book, taken
//          from http://norvig.com/spell-correct.html
//          Added look for each book in txt directory. By Nahtan Healea
            foreach ($books as $book => $row) {
                $lines = file($this->BigDirectory . DIRECTORY_SEPARATOR . $row);
                foreach ($lines as $line) {
//              Return all n grams from l after normalizing
                    $filtered_line = str_split(self::_normalise($line));
                    $a = false;
                    foreach ($filtered_line as $b) {
                        if ($a !== false) {
                            $log_prob_matrix[$pos[$a]][$pos[$b]] += 1;
                        }
                        $a = $b;
                    }
                }
//              Moved Unset here to clear variables after each use.
                unset($lines, $filtered_line);
            }


//          Normalize the counts so that they become log probabilities.
//          We use log probabilities rather than straight probabilities to avoid
//          numeric underflow issues with long texts.
//          This contains a justification:
//          http://squarecog.wordpress.com/2009/01/10/dealing-with-underflow-in-joint-probability-calculations/
            foreach ($log_prob_matrix as $i => $row) {
                $s = (float)array_sum($row);
                foreach ($row as $k => $j) {
                    $log_prob_matrix[$i][$k] = log($j / $s);
                }
            }

//          Find the probability of generating a few arbitrarily choosen good and
//          bad phrases.
            $good_lines = file($this->Good);
            $good_probs = array();
            foreach ($good_lines as $line) {
                array_push($good_probs, self::_averageTransitionProbability($line, $log_prob_matrix));
            }
//          Removed the bad lines probability. I see no need for this because it just lower the threshold.
//          and it just a score like the good_lines are
            /*$bad_lines = file($this->Bad);
            $bad_probs = array();
            foreach ($bad_lines as $line) {
                array_push($bad_probs, self::_averageTransitionProbability($line, $log_prob_matrix));
            }*/
//          Assert that we actually are capable of detecting the junk.
            /*$min_good_probs = min($good_probs);
            $max_bad_probs = max($bad_probs);

            if ($min_good_probs <= $max_bad_probs) {
                return false;
            }*/

//          And pick a threshold halfway between the worst good and best bad inputs.
            /*$threshold = ($min_good_probs + $max_bad_probs) / 2;*/
            $threshold = array_sum($good_probs) / count($good_probs);

//          save matrix
            return file_put_contents($this->Matrix, serialize(array(
                'matrix'    => $log_prob_matrix,
                'threshold' => $threshold,
            ))) > 0;
        }

        /* =================================
                Getters Function
        ================================== */

        /**
         * return the threshold of Gibberish Detector
         * @return Double
         */
        public function GetAverage()
        {
            $matrix = unserialize(file_get_contents($this->Matrix));
            return $matrix['threshold'];
        }

        public function GetBigDirectory()
        {
            return $this->BigDirectory;
        }
        public function GetAcceptedCharacters(){
            return self::$_accepted_characters;
        }
        public function GetGood(){
            return $this->Good;
        }
        public function GetBad(){
            return $this->Bad;
        }
        public function GetMatrix(){ return $this->Matrix; }

        /* =================================
                  Testing Helpers
        ================================== */
        public function Normalize($line){
            return $this->_normalise($line);
        }
        public function GetAverageTransitionProb($line, $log_prob_matrix)
        {
            return $this->_averageTransitionProbability($line, $log_prob_matrix);
        }
    }
