<h2>Scores</h2>
<p><strong>Threshold: </strong> <?php echo $threshold ?></p>
<?php if ($results != null) {
    foreach ($results as $text => $row) {
        ?>
        <div class="col-md-4">
            <div class="row text-output">
                <p><?php echo $row->body_text ?></p>
            </div>
            <div class="row">
                <?php
                    if ($row->is_gibberish == false) {
                        echo '<div class="alert-success text-score text-center"><p>' . $row->gibberish_score . '</p></div>';
                    } elseif ($row->is_gibberish == true) {
                        echo '<div class="alert-warning text-score text-center"><p>' . $row->gibberish_score . '</p></div>';
                    }
                ?>
            </div>
        </div>
    <?php }
} ?>
