
<h1>SHOWITEM</h1>

<p>You're looking at views/showitem/showitem_view.php </p>
<?php echo $this->item_nav; ?>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;">Original</div>
<div style="width:31%; float:left; padding:1%;">run_1</div>
<div style="width:31%; float:left; padding:1%;">live</div>
</div>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchdesc; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->comparedesc; ?></div>
<div style="width:31%; float:left; padding:1%;"><?php echo $this->fetchwasheddesc;  ?></div>
</div>
<div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
<?php print_r($this->itemlist); ?>    
</div>

