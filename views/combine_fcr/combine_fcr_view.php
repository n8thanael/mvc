<h1>COMBINE FCR PREVIEW</h1>

<p>You're looking at views/combine_fcr/combine_fcr_view.php </p>
<?php echo $this->item_nav; ?>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;">Original</div>
<div style="width:31%; float:left; padding:1%;">Modified</div>
<div style="width:31%; float:left; padding:1%;">Combined</div>
</div>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['name']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['new_name']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->comparerecord['name'];  ?></div>
</div>

<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['description']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['new_description']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->comparerecord['desc'];  ?></div>
</div>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['short']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchrecord[0]['new_short']; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->comparerecord['short'];  ?></div>
</div>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
</div>