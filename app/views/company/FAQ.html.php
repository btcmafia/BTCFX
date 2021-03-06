<?php
use app\models\Parameters;
$Comm = Parameters::find('first');
?>
<h3>FAQ</h3>

<p><strong><u>Become a Customer</u></strong></p>

<blockquote>To become an IBWT customer please click <a href="/users/signup">signup</a>. Registration implies you have read and agreed to our <a href="/company/termsofservice">Terms of Service.</a>
</blockquote>
<p><strong><u>Fees</u></strong></p>
<blockquote><ul>
<li>We charge <strong><?=$Comm['value']?></strong>% per transaction.</li>
<li>If you <strong>buy</strong> 1 Bitcoin our fee is <strong><?=$Comm['value']/100?></strong> Bitcoins.</li>
<li>If you <strong>sell</strong> &pound;100 worth of Bitcoins our fee is <strong><?=$Comm['value']*100?></strong> pence.</li>
</ul>
</blockquote>
<p><strong><u>Deposits/Withdrawals</u></strong></p>
<blockquote>

<ul>
<span>For <strong>bank wire transfer</strong> deposits/withdrawals please see <a href="/okpay">OKPAY</a>, read below for Royal Mail deposit/withdrawals.")?></span><br>
<span><strong>Royal Mail deposit/withdrawal only available for UK residents.</strong></span><br>
<span><strong>Limits</strong></span><br>
<ul>
<li><strong>Registered</strong> - &pound;2,500 daily.</li>
<li><strong>Verified</strong> - &pound;5,000 daily.</li>
<li><strong>Fully verified</strong> - &pound;20,000 daily.</li>
</ul>
    

    

		
<li>All deposits and withdrawals need to be verified and cleared, please see relevant sections when you login.</li>
<li>VERY IMPORTANT: Please make sure to INCLUDE your CUSTOMER REFERENCE with your deposit, which you can find when you complete FUNDING on your account page, so that we can credit your account appropriately.</li>
<li>We cannot be held liable if you send us money with no reference and have not completed a deposit request via your account (though with recorded delivery we can attempt to return any such fiat or solve such matters).</li>
<li>We cannot be held liable if you send us fiat with no reference, no deposit request, and no recorded delivery, and will treat such activity as suspicious and report it to the relevant authorities.</li>
<u>Example Reference:</u><br>
Account name: <strong>silent bob</strong><br>
Reference number: <strong>15828481</strong><br>
Amount: <strong>&pound;xxxx</strong><br>
</ul>  
<span>When we receive your funds we verify with your deposit request and credit your IBWT account the amount.</span><br>
<br>
<p><strong>Deposits</strong></p>
<ul >
<li>You mail fiat via Royal Mail.</li>
<li>Fiat deposits are currently done via Royal Mail.- <a href="http://www.royalmail.com/parcel-despatch-low" target="_blank">Parcel despatch.</a></li>
<li>Please make sure you read Royal Mails instructions for mailing fiat. - <a href="http://www.royalmail.com/business/help-and-support/what-is-the-best-way-to-send-money-or-jewellery" target="_blank">Royal Mail - What is the best way to send money or jewellery.</a></li>
<li>We strongly recommend that you pay for Royal Mail compensations, relevent charges can be found here. - <a href="http://www.royalmail.com/personal/uk-delivery/special-delivery">Royal Mail - Special delivery.</a></li>

<li>Royal Mail offers extra services which you may or may not want to use, such as earlier delivery (by 9am (more expensive but faster) or 2nd class mail (cheaper but slower)).</li>
<li><a href="http://www.royalmail.com/parcel-despatch-low/uk-delivery/special-delivery" target="_blank">Choose your options</a> or <a href="http://www.royalmail.com/parcel-despatch-low/uk-delivery/royal-mail-signed-2nd-class" target="_blank">2nd Class Mail</a></li>
<li>Royal Mail offers compensation cover of up to &pound;10,000. - <a href="http://www.royalmail.com/sites/default/files/RM%20Special%20Delivery%209am_Terms%20and%20Conditions_April%2012_0.pdf" target="_blank">Royal Mail Terms of Service.</a></li>
</ul>
<span>Once fiat amounts are received your account gets credited the same amount, just the same as doing a bank transfer, without the bank.</span><br>
<br>
<p><strong>Withdrawals</strong></p>
<ul>
<li>Please see <a href="/files/Withdrawal%20Verification.pdf" target="_blank">Withdrawal Verification</a> for instructions on how to input and verify your Proof of Address.</li>

<li>We charge customers the relevant fee that Royal Mail charges to cover the withdrawal amount respectively.</li>
<li>This charge is made to your IBWT account.</li>
<li>If you do not have enough to cover the Royal Mail fee in your IBWT account then your withdrawal will not be processed and you will be notified via email.</li>
<li>We store all fiat via safety deposit box services.</li>
</ul>
</blockquote>
<p><strong><u>Time Delays</u></strong></p>

<blockquote>
<ul >
<li>OKPAY withdrawals and deposits are processed within 24 hours.</li>
<li>Transfers are only processed weekdays, barring bank holidays.</li>
<li>It can take us up to 24 hours to verify and confirm your deposit request once received. Royal Mail takes 1-4 days to deliver, depending upon your choice of 1st or 2nd class.</li>
<li>It can take us up to 24 hours to verify, confirm and start the process for your withdrawal requests.</li>
<li>It can then take Royal Mail 1-3 days to deliver your withdrawal (we always use 1st Class).</li>
<li>We are not liable for Royal Mail incidents.</li>
</ul>
<u>Bitcoin</u>
<ul ><li>Bitcoin deposits and withdrawals are subject to the Bitcoin protocol.</li></ul>
<u>Litecoin</u>
<ul ><li>Litecoin deposits and withdrawals are subject to the Litecoin protocol.</li></ul>
</blockquote>

<p><strong><u>Security</u></strong></p>
<blockquote>

<ul >
<li>IBWT employs two factor authentication (2FA) and time-based one-time password algorithm (TOTP), for login, withdrawals, deposits and settings.</li>

<li>We also require a level of identification for all customers as per our (link) verification page, and run random security checks on accounts. Any information found to be out of date may result in the account in question to be temporarily suspended until such information is suitably updated.</li>

<li>If you have any issues please contact IBWT at <a href="mailto:support@ibwt.co.uk ">support@ibwt.co.uk</a></li>

</ul>
</blockquote>
<strong>Because of hard currency limits we reserve the right to change the limits IBWT has set in either direction and that we reserve the right to halt Royal Mail withdrawal until hard currency funds are replenished.</strong>
<small class="pull-right">Last updated on 30th January, 2014</small><br>