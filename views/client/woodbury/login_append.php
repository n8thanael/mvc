<?php

/* 
 * Appeneded to the woodbury login page if woodbury is logged in
 */

echo "Hi woodbury. <br>";
?>
<ul>
<li><a href="<?php echo URL; ?>showitem/fetch/1/run_1">Show Item Project (does not function yet)</a></li>
<li><a href="<?php echo URL; ?>flag_check/fetch/1/flagged_desc">Multi-Check</a></li>
<li><a href="<?php echo URL; ?>desc_fix/fetch/1/desc_fixes">Description-Fixes</a></li>
<li><a href="<?php echo URL; ?>desc_fix/fetch/2/shortie_warn_check">Shortie - Warning & Check</a></li>
<li><a href="<?php echo URL; ?>desc_fix/fetch/16/shortie_warn_auto_fixed"> Shortie - Warning & Auto-Fixed</a></li>
<li><a href="<?php echo URL; ?>desc_fix/fetch/3/auto_fixed">Auto-Fixed, Checking not necessary</a></li>
</ul>

