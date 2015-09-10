<h2>Scores</h2>
<p><strong>Threshold: </strong> <?php echo $threshold ?></p>
<?php if (isset($results)) { ?>
    <div class="col-lg-12">
        <table data-toggle="table"
               data-sort-name="score"
               data-sort-order="desc">
            <thead>
                <tr>
                    <th data-filed="body_text" data-sortable="true">Body Test</th>
                    <th data-filed="score" data-sortable="true">Score</th>
                    <th data-filed="is_gibberish" data-sortable="true">Gibberish ?</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $text => $row) { ?>
                    <tr>
                        <td><?php if (isset($row->body_text)) {
                                echo '<p class="text-output">' . $row->body_text . '</p>';
                            } ?></td>
                        <td><?php if (isset($row->gibberish_score)) {
                                echo htmlspecialchars($row->gibberish_score, ENT_QUOTES, 'UTF-8');
                            } ?></td>
                        <td><?php if (isset($row->is_gibberish)) {
                                if ($row->is_gibberish == false) {
                                    echo '<p class="alert-success text-center">False</p>';
                                } elseif ($row->is_gibberish == true) {
                                    echo '<p class="alert-warning text-center">True</p>';
                                }
                            } ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>