<!-- Start thank you message -->
<div class="message_text">
    <h1>Thank you for using The Motivator</h1>
</div>
<!-- End thank you message -->
<!-- Download Button -->
<div class="button_place">
    <button type="button">Download Poster</button>
</div>
<br>
<!-- Start image screen -->
<div id="get_image">
    <img src="<?php echo $this->data['dest']; ?>" alt="Your Image is Ready for Download">
</div>
<!-- End image screen -->
<!-- Start JS -->
<script type="text/javascript">
    $(document).ready(function() {
        // adjust font size for button //
        $(':button').css({
            'font-size': '1.4em',
            'cursor': 'pointer'
        });
        // Send for poster when button is clicked //
        $(':button').click(function() {
            window.location = '<?php echo $this->data['button_query']; ?>';
        });
    });
</script>
<!-- End JS -->