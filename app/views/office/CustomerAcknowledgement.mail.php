<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer">
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailHeader">
                            <tr>
                                <td align="center" valign="top">
                                  <!--  This is where my  content goes. -->
                                    <?php echo "<h1>" . COMPANY_NAME . "</h1>"; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody">
                            <tr>
                                <td align="left" valign="top">
                                  <!--  This is where my body content goes. -->

					<p>Dear <?=$visit['customer']['name']?>,</p>
				
					<p>Please confirm your request for a tradesperson appointment by clicking the link below. The details of your request are below, if any of the details are wrong you can send a message to the office by click here.</p>

					<p>As soon as you have confirmed your request, we will notify the tradesperson who will accept the job. No further action will be taken until you have confirmed your request. The rate you've been quoted is guaranteed as long as you confirm the request within 30 minutes.</p>

					<table border=1 cellpadding="5" width="90%" id="jobDetails">
					   <tr>
						<td colspan="2">
							<h3>Appointment Details</h3>
							<p>
							Service Category: <?=$visit['service_required']?><br />
							Job Description: <?=$visit['visit_title']?><br />
							Time &amp; Date: <?=$visit['time_and_date']['nicename']?>
							Comments: <?=$visit['comments']?><br />
							</p>
							<p>Hourly Rate: &pound;<?=$visit['rate']?> charged in 30 minute increments with a minimum charge of &pound;<?=$visit['min_charge']?>.

						</td>
					   </tr>
					   <tr>
						<td>
							<h4>Billing Details</h4>
							<p>
							<?=$visit['customer']['name']?><br />
							<?=$visit['billing_address']?><br />
							Tel: <?=$visit['customer']['phone']?><br />
							Email: <?=$visit['customer']['email']?>
							</p>
						</td>
						<td>
							<h4>Service Address</h4>
							<p>
							<?=$visit['service_address']['address']?><br />
							Contact name: <?=$visit['service_address']['contact_name']?><br />
							Tel: <?=$visit['service_address']['phone']?><br />
							Email: <?=$visit['service_address']['email']?>
							</p>
						</td>
					   </tr>

					   <tr>
						<td colspan="2">
						</td>
					   </tr>
					   <tr>
						<td colspan="2">
					<p>By clicking the link you are accepting our general terms of business.</p>

					<p><a href="<?php echo SITE_URL; ?>customers/viewbooking/<?=$job_id?>/<?=$visit_id?>/<?=$data['customer_id']?>/confirm/">Click here to confirm your request for a tradesperson.</a></p>

						</td>
					   </tr>

					</table>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
                            <tr>
                                <td align="center" valign="top">
                                    This is where my footer content goes.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
