<?php   $va_errors = $this->getVar("errors"); ?>
<form method="post" action="<?php print caNavUrl($this->request, 'formContact', 'formcontact', 'sendEmail'); ?>" name="emailForm" enctype='multipart/form-data'>
<!--<form action="FormToEmail.php" method="post"> -->
<table border="0" style="background:#ececec" cellspacing="5">
<tr align="left"><td><div class='formErrors'><?php echo $va_errors['subject'] ?></div><?php print _t("Subject"); ?></td><td><input type="text" name="subject" value="<?php print $vs_subject; ?>"></td></tr>
<tr align="left"><td><div class='formErrors'><?php echo $va_errors['from_name'] ?></div>Your Name</td><td><input type="text" size="30" name="from_name"></td></tr>
<tr align="left"><td><div class='formErrors'><?php echo $va_errors['from_email'] ?></div>Your Email address</td><td><input type="text" size="30" name="from_email"></td></tr>
<tr align="left"><td valign="top"><div class='formErrors'><?php echo $va_errors['message'] ?></div>Message to Send</td><td><textarea name="message" rows="6" cols="30"></textarea></td></tr>

<?php
						
						$vn_num1 = rand(1,10);
						$vn_num2 = rand(1,10);
						$vn_sum = $vn_num1 + $vn_num2;
?>
<tr align="left"><td valign="top">
							<?php print ($va_errors["security"]) ? "<div class='formErrors'>".$va_errors["security"]."</div>" : ""; ?>
							<?php print _t("Security Question (to prevent SPAMbots)"); ?><br/>
							<?php print $vn_num1; ?> + <?php print $vn_num2; ?> = <input name="security" value="" id="security" type="text" size="3" style="width:50px;" />

						<input type="hidden" name="sum" value="<?php print $vn_sum; ?>">
</td></tr>

<tr align="left"><td>&nbsp;</td><td><input type="submit" value="Send"></td></tr>
</table>
</form>
