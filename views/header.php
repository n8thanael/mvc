<?php ?>
<!DOCTYPE html>
<html>
    <head>
        <title>MVC Experience</title>
        <link rel="stylesheet" href="<?php echo URL;?>public/css/default.css"/>
        <script type="text/javascript" src="<?php echo URL;?>public/js/jquery.js"></script>
        <!-- <script type="text/javascript" src="<?php echo URL;?>public/js/custom.js"></script> -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script>tinymce.init({ selector:'#MCEtextarea', menu: {}, plugins: "autoresize code", toolbar: "code | undo redo | bullist numlist outdent indent | bold | removeformat "  });</script>
        <script>tinymce.init({ selector:'#MCEtextareaB',menu: {}, plugins: "autoresize code", toolbar: "code | undo redo | bullist numlist outdent indent | bold | removeformat "  });</script>        
    </head>
    <body>

        <div id="header">header</div>
        <a href="<?php echo URL;?>index">index</a>
        <a href="<?php echo URL;?>help">help</a>
        <?php if (Session::get('loggedIn') == true):?>
            <a href="<?php echo URL;?>login/logout">Logged in as:  <?php echo Session::get('user'); ?></a>
        <?php else: ?>
            <a href="<?php echo URL;?>login">login</a>
        <?php endif;?>

        <div style = "float:left;" id="content">