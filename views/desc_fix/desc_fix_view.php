<script>
$(document).ready(function () {


    var toggle=0;
      $(".trigger").on("click",function() {
       if(toggle==0){
        $('.switch').find('.regular').hide();
        $('.switch').find('.code').show();
        $('.switchB').find('.regular').hide();
        $('.switchB').find('.code').show();       
           toggle=1;
           return;
       }
       if(toggle==1){
        $('.switch').find('.regular').show();
        $('.switch').find('.code').hide();
        $('.switchB').find('.regular').show();
        $('.switchB').find('.code').hide();          
           toggle=0;
           return;
       }

    });
});
</script>
<style>
textarea  
{  
   font-family:"Times New Roman", Times, serif;
   width:100%;
   min-height:275px;
}
.code {
display: none;
}

.avg_num {
	display: none;
}
</style>

<form action="<?php echo $this->form_action; ?>" method="post">
        <?php echo $this->item_nav; ?>
        <?php if(!empty($this->e)){ echo ' | <span style="color:red;font-weight:bold;">error: ' . $this->e . "</span.";} ?> 
        <?php if(!empty($this->s)){ echo ' | <span style="color:green;font-weight:bold;">success: ' . $this->s . "</span>";} ?> 
    <div style="width:100%; float:right;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div style="float:left;">
            <div style="font-size:1.5em; display:inline; padding:5px; background-color:yellow;"><?php echo($this->record[0]['status']. '</div><div style="font-size:1.5em; display:inline; padding:5px;">' .$this->current_sort); ?></div>
        </div><div style="float:right;"> 
            <input type="submit"  name="submit" value="Skip">
            <input type="submit"  name="submit" value="Approved">
            <input type="submit" name="submit" value="Apply Updates">
            <input type="submit" name="submit" value="Visual Inspection Required">
            <input type="submit"  name="submit" value="Error">
            <input type="hidden" name="paramstring" value="<?php echo $this->paramstring; ?>">
            <div class="trigger" style="padding:3px; display: inline-block; background-color:grey;">CODE</div>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div style="width:31%; float:left; padding:1%;"><a href="<?php echo $this->picinfo[0]['url'] ?>" target="_blank">
        <!-- <img style="max-height:80px;" src="https://www.woodburyoutfitters.com/prodimages/< php echo $this->picinfo[0]['picture_id']; ?>"> -->
        <img style="max-height:80px;" src="<?php echo $this->record[0]['ca_image']; ?>">
        </a>
        </div>
        <div style="width:31%; float:left; padding:1%;"><?php echo $this->originalname; ?>
            <input style ="width:100%;" type="text" name="originalname" value="<?php echo $this->flagname; ?>"></div>
        <div style="width:10%; float:left; padding:2px;">
            <ul style="list-style:none; padding:0px; margin:0px;">
                <li><span>Style: </span><?php echo($this->record[0]['style']); ?></li>
                <li><span>STYLE id: </span><?php echo($this->record[0]['style_id']); ?></li>
                <li><span>Store id: </span><?php echo($this->record[0]['store_id']); ?></li>
                <li><span>OF19: </span><?php echo($this->record[0]['OF19']); ?></li>
            </ul>
        </div><div style="width:22%; float:left; padding:2px;">
            <ul style="list-style:none; padding:0px; margin:0px;">
                <li><span>Brand: </span><?php echo($this->record[0]['brand']); ?></li>
                <li><span>Dept: </span><?php echo($this->record[0]['dept']); ?></li>
                <li><span>Class: </span><?php echo($this->record[0]['class']); ?></li>
                <li><span>Price: </span>$<?php echo($this->record[0]['price']); ?></li>
                <li><span>CA_INV#: </span><?php echo($this->record[0]['style_id'] . "-" . $this->record[0]['sku_id'] . "-" . $this->record[0]['store_id'] ); ?></li>
            </ul>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block; min-height:300px;">
        <div style="width:100%; background-color: orange;text-align:center; color:white;"><?PHP if ($this->flagshort != ''){echo 'SHORT DESCRIPTION BELOW';}; ?></div>
        <div class="switch">
            <div class="regular" style="float:left; width:31%; padding:1%;"><?php echo $this->originaldesc; ?></div>
            <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->originaldesc; ?></xmp></div>
        </div>
        <div class="switchB">
        <div class="regular" style="width:31%; float:left; padding:1%;"><textarea id="MCEtextarea" name="originaldesc"><?php echo $this->flagdesc; ?></textarea></div>
        <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->flagdesc; ?></xmp></div>
        </div>
        <div class="switch">
            <div class="regular" style="float:left; width:31%; padding:1%;"><?php echo $this->diffdesc; ?></div>
            <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->diffdesc; ?></xmp></div>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
        <div class="switch">
            <div class="regular" style="float:left; width:31%; padding:1%;"><?php echo $this->originalshort; ?></div>
            <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->originalshort; ?></xmp></div>
        </div>
        <div class="switchB">
        <div class="regular" style="width:31%; float:left; padding:1%;"><textarea id="MCEtextareaB" name="originalshort"><?php echo $this->flagshort; ?></textarea></div>
        <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->flagshort; ?></xmp></div>
        </div>
        <div class="switch">
            <div class="regular" style="float:left; width:31%; padding:1%;"><?php echo $this->diffshort; ?></div>
            <div class="code" style="float:left; width:31%; padding:1%;"><xmp style="white-space: pre-wrap" ><?php echo $this->diffshort; ?></xmp></div>
        </div>
    </div>
    <div style="width:100%; float:left;border-top:solid 2px #CCCCCC;padding:5px; display:block;">
    <div class="trigger" style="padding:3px; display: inline-block; background-color:grey;">CODE</div>
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
