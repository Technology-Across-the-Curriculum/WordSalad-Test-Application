<p><strong>Threshold:</strong><?php echo $this->model->GetAverage() ?></p>
<?php
    for ($i = 0;$i < count($resultArray["text"]);$i++) {
        ?>
        <div class="col-md-4 table-responsive table-bordered">
            <div class="row text-output">
                <p><?php echo $resultArray["text"][$i] ?></p>
            </div>
            <div class="row">
                <?php
                    if ($resultArray["score"][$i] >= $this->model->GetAverage()) {
                        echo '<div class="label-success text-score text-center"><p>' . $resultArray["score"][$i] . '</p></div>';
                    } else {
                        if ($resultArray["score"][$i] < $this->model->GetAverage()) {
                            echo '<div class="label-warning text-score text-center"><p>' . $resultArray["score"][$i] . '</p></div>';
                        }
                    }
                ?>
            </div>
        </div>
    <?php } ?>