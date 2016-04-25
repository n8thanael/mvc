<?php ?>
<!DOCTYPE html>
<html>
    <head>
        <title>MVC Experience</title>
        <link rel="stylesheet" href="<?php echo URL;?>public/css/default.css"/>
        <script type="text/javascript" src="public<?php echo URL;?>/js/jquery.js"></script>
        <!-- <script type="text/javascript" src="public<?php echo URL;?>/js/custom.js"></script> -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script>tinymce.init({ selector:'#MCEtextarea' });</script>
        <script>tinymce.init({ selector:'#MCEtextareaB' });</script>        
    </head>
    <body>

        <div id="header">header</div>
        <a href="<?php echo URL;?>index">index</a>
        <a href="<?php echo URL;?>help">help</a>
        <?php if (Session::get('loggedIn') == true):?>
            <a href="<?php echo URL;?>login/logout">logout</a>
        <?php else: ?>
            <a href="<?php echo URL;?>login">login</a>
        <?php endif;?>

        <div style = "float:left;" id="content">