<div class="container">
    <div class="jumbotron text-center">
        <h1>WordSalad</h1>

        <p><strong>Contributors</strong></p>
        <?php foreach ($about as $line => $row) {
            echo '<p>' . $row . '</p>';
        }
        ?>
    </div>
</div>
