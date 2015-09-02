<?php

    class Model
    {
        public $db = null;

        /**
         * @param object $db A PDO database connection
         */
        function __construct()
        {
            try {
                $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
                $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                exit('Database connection could not be established.');
            }
        }
        public function PrintArray($array, $header)
        {
            $count = 0;
            $letter = Array();
            echo '<table class="table table-responsive">';
            echo '<thead><tr>';
            echo '<th> </th>';
            foreach ($header as $l => $row) {
                echo '<th>' . $l . '</th>';
                array_push($letter, $l);
            }
            echo '</tr></thead>';

            echo '<tbody>';
            foreach ($array as $a => $row) {
                echo '<tr>';
                echo '<td><strong> ' . $letter[$count] . '</strong></td>';
                $count++;
                foreach ($row as $b => $j) {

                    echo '<td>' . $array[$a][$b] . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
