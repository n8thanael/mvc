<script>
function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (25+o.scrollHeight)+"px";
}

</script>
<style>
textarea  
{  
   font-family:"Times New Roman", Times, serif;
   width:100%;
   min-height:275px;
}
</style>

<form action="<?php echo $this->form_action; ?>" method="post">
        <?php echo $this->item_nav; ?>
        <?php if(!empty($this->e)){ echo ' | <span style="color:red;font-weight:bold;">error: ' . $this->e . "</span.";} ?> 
        <?php if(!empty($this->s)){ echo ' | <span style="color:green;font-weight:bold;">success: ' . $this->s . "</span>";} ?> 
    <div style="width:100%; float:right;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div style="float:left;">
            <div style="font-size:1.5em; display:inline; padding:5px; background-color:yellow;"><?php echo($this->comparerecord[0]['status']. '</div><div style="font-size:1.5em; display:inline; padding:5px;">' .$this->current_sort); ?></div>
        </div><div style="float:right;"> 
            <input type="submit"  name="submit" value="Approved">
            <input type="submit" name="submit" value="Apply Updates">
            <input type="submit" name="submit" value="Visual Inspection Required">
            <input type="submit"  name="submit" value="Error">
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->comparename; ?></div>
        <div style="width:31%; float:left; padding:1%;"><input style ="width:100%;" type="text" name="comparename" value="<?php echo $this->fetchname; ?>"></div>
        <div style="width:10%; float:left; padding:2px;">
            <ul style="list-style:none; padding:0px; margin:0px;">
                <li><span>Style: </span><?php echo($this->comparerecord[0]['style']); ?></li>
                <li><span>STYLE id: </span><?php echo($this->comparerecord[0]['style_id']); ?></li>
                <li><span>Store id: </span><?php echo($this->comparerecord[0]['store_id']); ?></li>
                <li><span>OF19: </span><?php echo($this->comparerecord[0]['OF19']); ?></li>
            </ul>
        </div><div style="width:22%; float:left; padding:2px;">
            <ul style="list-style:none; padding:0px; margin:0px;">
                <li><span>Brand: </span><?php echo($this->comparerecord[0]['brand']); ?></li>
                <li><span>Dept: </span><?php echo($this->comparerecord[0]['dept']); ?></li>
                <li><span>Class: </span><?php echo($this->comparerecord[0]['class']); ?></li>
                <li><span>Price: </span>$<?php echo($this->comparerecord[0]['price']); ?></li>
            </ul>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block; min-height:300px;">
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->comparedesc; ?></div>
         <!-- onkeyup="textAreaAdjust(this)" style="overflow:hidden;" -->
         <!--<?php echo $this->fetchdesc; ?> --> 
        <div style="width:31%; float:left; padding:1%;"><textarea id="MCEtextarea" name="comparedesc"><?php echo $this->fetchdesc; ?></textarea></div>
        <div style="width:31%; float:left; padding:1%;"><a href="<?php echo $this->fetchpicinfo[0]['url'] ?>" target="_blank">
                <img src="https://www.woodburyoutfitters.com/prodimages/<?php echo $this->fetchpicinfo[0]['picture_id']; ?>-DEFAULT-s.jpg">
            </a>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->compareshort; ?></div>
        <div style="width:31%; float:left; padding:1%;"><textarea id="MCEtextareaB" name="compareshort"><?php echo $this->fetchshort; ?></textarea></div>
        <div style="width:31%; float:left; padding:1%;">nada</div>
    </div>
    
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <p><?php echo $this->item_nav; ?></p>   
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <h3><?php echo $this->status_message; ?></h3>

        <div style="width:31%; float:left; padding:1%;"><?php echo $this->deptstring; ?></div>
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->fbrandstring; ?></div>  
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->statusstring; ?></div> 
    </div>
</form>


