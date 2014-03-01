<!-- Begin image screen -->
<div id="welcome">
    <h1>Welcome to the Motivator</h1>
    <?php
    // Used if there are images in the poster directory //
    foreach ($this->data['images'] as $im) {
        $path = $im['path'];
        $image = $im['name'];
        $div = <<<EOD
<div class="image">
    <a href="{$path}" title="{$image}">
    <img src="{$path}" alt="{$image}">
    </a>
    </div>
    <br>
EOD;
        echo $div;
    }
    ?>
</div>
<!-- End image screen -->