<div class="jumbotron">
    <h1>Testing WorldSalad</h1>

    <p><strong>Threshold: </strong> <?php echo $threshold; ?></p>
</div>
<div class="col-lg-12">
    <table data-toggle="table"
           data-sort-name="table_id"
           data-sort-order="desc">
        <thead>
            <tr>
                <th data-filed="table_id" data-sortable="true">Id</th>
                <th data-filed="time_stamp" data-sortable="true">Time Stamp</th>
                <th data-filed="threshold" data-sortable="true">Threshold</th>
                <th>DETAILS</th>
                <th>DELETE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allTest as $test) { ?>
                <tr>
                    <td><?php if (isset($test->id)) echo htmlspecialchars($test->id, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php if (isset($test->test_time)) echo htmlspecialchars($test->test_time, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php if (isset($test->threshold)) echo htmlspecialchars($test->threshold, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="<?php echo URL . 'TestController/testScores/' . htmlspecialchars($test->id, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="btn btn-default">Details </div>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo URL . 'TestController/deleteTest/' . htmlspecialchars($test->id, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="btn btn-danger">Delete</div>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>