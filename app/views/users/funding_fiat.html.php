<style>
.Address_success{background-color: #9FFF9F;font-weight:bold}
</style>
<h3 class="alert alert-error">Please ensure that your Royal Mail deposits are sent with appropriate cover.</h3>
<?php echo $this->_render('element', 'funding_fiat_header');?>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-info" >
			<div class="panel-heading">
			<h2 class="panel-title">Deposit USD / GBP / EUR / CAD </h2>
			</div>
		</div>
		<form action="/users/deposit/" method="post" class="form">
		<table class="table table-condensed table-bordered table-hover" style="margin-top:-20px">
			<tr>
				<td>Deposit Methods:</td>
				<td>
					<select name="DepositMethod" id="DepositMethod" onChange="DepositByMethod(this.value);" class="form-control">
						<option value="okpay">OKPAY</option>
						<option value="post">Postal Address - Royal Mail</option>
					</select>
				</td>
			</tr>
			<tr style="background-color:#CFFDB9">
				<td colspan="2">Send payment to</td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="DepositPost" style="display:none">
						<table>
							<tr>
								<td>Registered Address: </td>
								<td>IBWT JD Ltd<br>
									 31 North Down Crescent<br>
									 Keyham, Plymouth<br>
									 Devon, PL2 2AR<br>
									United Kingdom</td>
							</tr>
						</table>
					</div>
					<div id="DepositOkPay" style="display:block">
						<p>Please make SURE to include your IBWT reference in the COMMENT of your OKPAY transaction to: deposit@ibwt.co.uk through <a href="/okpay" target="_blank"><strong>OKPAY</strong></a></p>
					</div>
				</td>
			<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Quote this reference number in your deposit">
				<td>Reference:</td>
				<?php $Reference = substr($details['username'],0,10).rand(10000,99999);?>
				<td><?=$Reference?></td>
			</tr>
			<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Amount should be between 1 and 10000">
				<td>Amount:</td>
				<td><input type="text" value="" class="form-control" placeholder="1.0" min="1" max="10000" name="AmountFiat" id="AmountFiat" maxlength="5"></td>
			</tr>
			<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Select a currency">
				<td>Currency:</td>
				<td><select name="Currency" id="Currency" class="form-control" >
						<option value="<?=$currency?>"><?=$currency?></option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2">
				<div id="MailSelect" style="display:none ">
				<p><strong><a href="/company/funding" target="_blank">Please make SURE you check with Royal Mail compensation cover and how to deposit BEFORE sending any funds!</a> </strong></p>
				</div>
				<div id="OkPaySelect" style="display:block ">									
				<p><strong><a href="/okpay" target="_blank">You will need a verified OKPAY account to deposit via bank wire transfers</a></strong></p>
				</div>
				</td>
			</tr>
			<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Once verified and processed your funds will be mailed.">
				<td colspan="2" style="text-align:center ">
				<input type="hidden" name="Reference" id="Reference" value="<?=$Reference?>">
					<input type="submit" value="Send email to admin for approval" class="btn btn-primary" onclick="return CheckDeposit();">
				</td>
			</tr>
		</table>
		</form>

	</div>
	<div class="col-md-6">
		<div class="panel panel-info" >
			<div class="panel-heading">
			<h2 class="panel-title">Withdraw <?=$currency?> </h2>
			</div>
		</div>
			<form action="/users/withdraw/" method="post" class="form">		
			<table class="table table-condensed table-bordered table-hover" style="margin-top:-20px">
				<tr style="background-color:#CFFDB9">
					<td>Balance</td>
					<td style="text-align:right " colspan="2"><?=$details['balance.'.$currency]?> <?=$currency?></td>									</tr>			
				<tr style="background-color: #FDDBAC">
				<?php 
				$Amount = 0;$AmountUSD = 0;$AmountEUR = 0; $AmountCAD = 0;
				foreach($transactions as $transaction){
					if($transaction['Currency']==$currency){
						$Amount = $Amount + $transaction['Amount'];
					}
				}
				?>
					<td>Withdrawal</td>
					<td style="text-align:right " colspan="2"><?=$Amount?> <?=$currency?></td>					
				</tr>			
				<tr style="background-color:#CFFDB9">
					<td>Net Balance</td>
					<td style="text-align:right " colspan="2"><?=$details['balance.'.$currency]-$Amount?> <?=$currency?></td>					
				</tr>							
				<tr>
					<td>Withdrawal Methods:</td>
					<td colspan="2">
						<select name="WithdrawalMethod" id="WithdrawalMethod" onChange="PaymentMethod(this.value);" class="form-control">
							<option value="okpay">OKPAY</option>
							<option value="post">Postal Address - Royal Mail</option>
							<option value="bank">Bank - Personal</option>
							<option value="bankBuss">Bank - Business</option>											
						</select>
					</td>
				</tr>
				<tr>
				<td colspan="5">
					<div id="WithdrawalOKPAY" style="display:block">
					<?php if($details['okpay']['verified']=='Yes'){?>
					<p>Please use your verified IBWT email with your OKPAY account. You can add your IBWT email address to your OKPAY account even if you used a different email to open your account. We will send the funds to your primary email address: <strong><?=$details['okpay']['email']?></strong></p>
					<input type="hidden" name="okpay_email" value="<?=$details['okpay']['email']?>">
					<?php }else{?>
					<p>Please use your verified IBWT email with your <a href="/users/settings">OKPAY</a> account. You can add your IBWT email address to your <a href="/users/settings">OKPAY</a> account even if you used a different email to open your account. We will send the funds to your primary email address: <strong><?=$user['email']?></strong></p>
					<input type="hidden" name="okpay_email" value="<?=$user['email']?>">
					<?php }?>
					</div>
					<div id="WithdrawalBank" style="display:none">
				<table class="table table-condensed table-bordered table-hover">								
					<tr>
						<td>Account name:</td>
						<td><input type="text" name="AccountName" id="AccountName" placeholder="Verified bank account name" value="<?=$details['bank']['bankname']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Sort code: </td>
						<td><input type="text" name="SortCode" id="SortCode" placeholder="01-01-10" value="<?=$details['bank']['sortcode']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Account number:</td>
						<td><input type="text" name="AccountNumber" id="AccountNumber" placeholder="12345678" value="<?=$details['bank']['accountnumber']?>" class="form-control"></td>
					</tr>
					</table>
					</div>
					<div id="WithdrawalBankBuss" style="display:none">
				<table class="table table-condensed table-bordered table-hover">								
					<tr>
						<td>Account name:</td>
						<td><input type="text" name="AccountNameBuss" id="AccountNameBuss" placeholder="Verified bank account name" value="<?=$details['bankBuss']['bankname']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Sort code: </td>
						<td><input type="text" name="SortCodeBuss" id="SortCodeBuss" placeholder="01-01-10" value="<?=$details['bankBuss']['sortcode']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Company name:</td>
						<td><input type="text" name="CompanyNameBuss" id="CompanyNameBuss" placeholder="12345678" value="<?=$details['bankBuss']['companyname']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Company number:</td>
						<td><input type="text" name="CompanyNumberBuss" id="CompanyNumberBuss" placeholder="12345678" value="<?=$details['bankBuss']['companynumber']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Account number:</td>
						<td><input type="text" name="AccountNumberBuss" id="AccountNumberBuss" placeholder="12345678" value="<?=$details['bankBuss']['accountnumber']?>" class="form-control"></td>
					</tr>
					</table>
					</div>									
					<div id="WithdrawalPost"  style="display:none">
					<table class="table table-condensed table-bordered table-hover">
					<tr>
						<td>Name:</td>
						<td><input type="text" name="PostalName" id="PostalName" placeholder="Name" value="<?=$details['postal']['Name']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Address:</td>
						<td><input type="text" name="PostalAddress" id="PostalAddress" placeholder="Name" value="<?=$details['postal']['Address']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Street:</td>
						<td><input type="text" name="PostalStreet" id="PostalStreet" placeholder="Street" value="<?=$details['postal']['Street']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>City:</td>
						<td><input type="text" name="PostalCity" id="PostalCity" placeholder="City" value="<?=$details['postal']['City']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Postal / Zip code:</td>
						<td><input type="text" name="PostalZip" id="PostalZip" placeholder="Zip" value="<?=$details['postal']['Zip']?>" class="form-control"></td>
					</tr>
					<tr>
						<td>Country:</td>
						<td><input type="text" name="PostalCountry" id="PostalCountry" placeholder="Country" value="<?=$details['postal']['Country']?>" class="form-control"></td>
					</tr>
				<tr>
					<td>Withdrawal Charges </td>
					<td>
					<input type="radio" name="WithdrawalCharges" value="PriceFinder" id="WithdrawalCharges" class="form-control">
				<strong>1st Class</strong> <a href="http://www.royalmail.com/price-finder" target="_blank">Price Finder</a><br>
					&pound;50 = &pound;1.70<br>
					&pound;500 = &pound;6.22<br>
					&pound;1,000 = &pound;19.84<br>
					&pound;2,500 = &pound;23.34<br>
						<input type="radio" name="WithdrawalCharges" value="PostalOrder" id="WithdrawalCharges" class="form-control">
					<a href="http://www.postoffice.co.uk/postal-orders" target="_blank">Postal Order</a><br>
					&pound;0.50 - &pound;4.99 = 50p<br>
					&pound;5 - &pound;9.99 = &pound;1.00<br>
					&pound;10.00 - &pound;99.99 = 12.50%<br>
					&pound;100 - &pound;250 = 12.50%<br>
					</td>
				</tr>
				<tr>
					<td colspan="5"><p><strong>Make SURE you choose the appropriate Royal Mail charge to cover the amount you are withdrawing and that your IBWT account contains enough to cover the charge. Otherwise your withdrawal will be declined by IBWT.</strong></p></td>
				</tr>
					
					</table>
					</div>
			</td>
				<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Quote this reference number in your withdrawal">
					<td >Reference:</td>
					<?php $Reference = substr($details['username'],0,10).rand(10000,99999);?>
					<td colspan="2"><?=$Reference?></td>
				</tr>
				<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Amount should be between 6 and 10000">
					<td>Amount:</td>
					<td colspan="2"><input type="text" value="" placeholder="5.0" min="5" max="10000" name="WithdrawAmountFiat" id="WithdrawAmountFiat" maxlength="5" class="form-control"><br>
<small style="color:red ">
&pound;1 mail withdrawal fee + royal mail fee (see below).<br>
&pound;1 OKPAY withdrawal fee<br>
&pound;2 bank withdrawal fee.<br>
Mail withdrawals must be in denominations of &pound;5.<br>
</small></td>
				</tr>
				<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Select a currency">
					<td>Currency:</td>
					<td colspan="2"><select name="WithdrawCurrency" id="WithdrawCurrency" class="form-control">
							<option value="<?=$currency?>"><?=$currency?></option>																		
					</select></td>
				</tr>
				<tr  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Once your email is approved, you will receive the funds in your bank account">
					<td colspan="3" style="text-align:center ">
					<input type="hidden" name="WithdrawReference" id="WithdrawReference" value="<?=$Reference?>" class="form-control">
					<input type="submit" value="Send email to admin for approval" class="btn btn-primary" onclick="return CheckWithdrawal();" >
					</td>
				</tr>
			</table>
		</form>
	</div>
	See our <a href="/withdrawals">Withdrawal</a> information to see current status of withdrawals. Please consider withdrawals via <a href="/okpay">OKPAY</a> if Hard Currency is unavailable.
</div>
