<!-- Start Container -->
<div id="container">
    <!-- Start example poster -->
    <div id="frame">
        <img src="images/site/frame.png" alt="example image"  height="240" width="300">
    </div>
    <!-- End example poster -->
    <!-- Start form submission -->
    <form id="upload_form" action="index.php?upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->data['max_file_size'] ?>">
        <label for="upload">Upload image:</label>
        <input type="file" id="upload" name="file_upload">
        <label for="title_text">Title:</label>
        <input type="text" id="title_text" class="text_input" name="title_text" value="<?php echo (isset($_POST['title_text'])) ? $_POST['title_text'] : ''; ?>">
        <label for="message_text">Message:</label>
        <input type="text" id="message_text" class="text_input" name="message_text" value="<?php echo (isset($_POST['message_text'])) ? $_POST['message_text'] : ''; ?>">
        <br>
        <input type="submit" name="submit" value="Motivate!">
    </form>
    <!-- End form submission -->
</div>
<!-- End container -->
<!-- Start upload guide -->
<div id="guide">
    <ul>
        <?php
        // Display each item as a list item //
        foreach ($this->data['guide_array'] as $item) {
            echo '<li>' . $item . '</li>' . PHP_EOL;
        }
        ?>
    </ul>
</div>
<div id="loading">
    <p></p>
</div>
<!-- Guide button placeholder -->
<div class="button_place">
    <button type="button" name="guide_button" onclick="show_guide('#container', '#guide')">Show Guide</button>
</div>
<!-- End upload Guide -->
<!-- Start poster text -->
<div id="poster_text">
    <h1>THE MOTIVATOR</h1>
    <h2>create your own motivational poster!</h2>
</div>
<!-- End poster text -->
<!-- Start JS -->
<script type="text/javascript">
    $(document).ready(function() {
        // Sets error variable //
        var error = <?php echo (isset($this->data['js_error'])) ? $this->data['js_error'] : '[]'; ?>;
        // display errors in guide //
        if (error.length > 0) {
            show_guide('#container', '#guide');
            // Iterate through errors and select li items //
            $.each(error, function(index, value) {
                $('li:contains(' + value + ')').addClass('warning');
            });
        }
        // Shows loading frame and hides the guide button //
        $(':submit').click(function() {
            loading('#container', '#loading');
            $(':button[name="guide_button"]').prop('disabled', true);
        });
    });
</script>
<!-- End JS -->