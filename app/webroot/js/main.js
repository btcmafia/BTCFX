// JS Document

/* Open when someone clicks on the span element */
function openNav() {
    document.getElementById("myNav").style.width = "100%";
}

/* Close when someone clicks on the "x" symbol inside the overlay */
function closeNav() {
    document.getElementById("myNav").style.width = "0%";
    document.getElementById("myNav").style.class = "hide";
}

function showService() {
    document.getElementById("service_address_form").style.class = "hide";
return false;
}

function newCustomer() {

$('form').submit(function(event) {

var formData = {
            'first_name'        : $('input[id=name]').val(),
	    'last_name'		: $('input[id=last_name]').val(),
            'phone'             : $('input[id=phone]').val(),
            'email'             : $('input[id=email]').val(),
            'address_1'         : $('input[id=address_1]').val(),
            'address_2'         : $('input[id=address_2]').val(),
            'city'              : $('input[id=city]').val(),
            'postcode'          : $('input[id=postcode]').val(),
        };

        // process the form
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : '/office/newcustomer/', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true
        },

 function(ReturnValues){



                // log data to the console so we can see
                console.log(ReturnValues); 

                // here we will handle errors and validation messages
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
});

}

function UpdateDetails(ex){
	var delay = 10000;
	var now, before = new Date();
	GetDetails(ex,delay/1000);
	setInterval(function() {
    now = new Date();
    var elapsedTime = (now.getTime() - before.getTime());
    GetDetails(ex,delay/1000);
    before = new Date();    
}, delay);
	
}
function GetDetails(ex,counter){
	var refreshID = setInterval(function() {
		counter = counter - 1;
		$("#Timer").html(counter);
		if(counter <= 0){
			clearInterval(refreshID);
			return;
			}
	}	,1000);	
	user_id = $("#User_ID").html();
	if(ex=="/EX/DASHBOARD"){ex = "BTC/GBP";}
	CheckServer();
	$.getJSON('/Updates/Rates/'+ex,
		function(ReturnValues){
			if(ReturnValues['Refresh']=="Yes"){
					$.getJSON('/Updates/Orders/'+ex,
						function(Orders){
							$('#BuyOrders').html(Orders['BuyOrdersHTML']);
							$('#SellOrders').html(Orders['SellOrdersHTML']);							
					});
					$.getJSON('/Updates/YourOrders/'+ex+'/'+user_id,
						function(Orders){
							$('#YourCompleteOrders').html(Orders['YourCompleteOrdersHTML']);
							$('#YourOrders').html(Orders['YourOrdersHTML']);							
					});
			}
			
			$("#LowPrice").html(ReturnValues['Low']);
			$("#HighPrice").html(ReturnValues['High']);					
			$("#LowestAskPrice").html(ReturnValues['High']);	
			if($("#BuyPriceper").val()=="" || $("#BuyPriceper").val()==0){
				$("#BuyPriceper").val(ReturnValues['High']);
			}
			$("#HighestBidPrice").html(ReturnValues['Low']);
			if($("#SellPriceper").val()=="" || $("#SellPriceper").val()==0){
				$("#SellPriceper").val(ReturnValues['Low']);
			}
			$("#LastPrice").html(ReturnValues['Last']);
			Volume = ReturnValues['VolumeFirst'] + " " + ReturnValues['VolumeFirstUnit'] +
			" / " + ReturnValues['VolumeSecond'] + " " + ReturnValues['VolumeSecondUnit'];
			$("#Volume").html(Volume);					
		});
}
function CheckServer(){
		$.getJSON('/Updates/CheckServer/',
		function(ReturnValues){
			if(ReturnValues['Refresh']=="No"){
				window.location.assign("/login");								
			}
		});
}

function BuyFormCalculate (){
	BalanceSecond = $('#BalanceSecond').html();
	FirstCurrency = $('#BuyFirstCurrency').val();
	SecondCurrency = $('#BuySecondCurrency').val();
	BuyAmount = $('#BuyAmount').val();
	BuyPriceper = $('#BuyPriceper').val();
	if(BuyAmount=="" || BuyAmount<=0){
			$("#BuySummary").html("Amount less than Zero!");
			$("#BuySubmitButton").attr("disabled", "disabled");
			$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
		return false;
	}
	if(BuyAmount>9999){
			$("#BuySummary").html("Amount greater than 10000!");
			$("#BuySubmitButton").attr("disabled", "disabled");
			$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
		return false;
	}
	if(BuyPriceper=="" || BuyPriceper<=0){
		$("#BuySummary").html("Price less than Zero!");
		$("#BuySubmitButton").attr("disabled", "disabled");
		$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
		return false;
	}
	if(BuyPriceper>9999){
		$("#BuySummary").html("Price greater than 10000!");
		$("#BuySubmitButton").attr("disabled", "disabled");
		$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
		return false;
	}

	TotalValue = BuyAmount * BuyPriceper;
	TotalValue = TotalValue.toFixed(6);
	$("#BuyTotal").html(TotalValue);
	
	$.getJSON('/Updates/Commission/',
		function(ReturnValues){
			$("#BuyCommission").val(ReturnValues['Commission']);			
			Commission = $('#BuyCommission').val();
			Fees = BuyAmount * Commission / 100;
			Fees = Fees.toFixed(5);
			if(Fees<=0){return false;}
			$("#BuyFee").html(Fees);	
			$('#BuyCommissionAmount').val(Fees);
			$('#BuyCommissionCurrency').val(FirstCurrency);			
		}
	);
	GrandTotal = TotalValue;
	if(GrandTotal==0){
		BuySummary = "Amount cannot be Zero";
		$("#BuySummary").html(BuySummary);
		$("#BuySubmitButton").attr("disabled", "disabled");
		$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
		return false;
	}
	if(parseFloat(BalanceSecond) <= parseFloat(GrandTotal)){
		Excess = parseFloat(GrandTotal) - parseFloat(BalanceSecond);
		Excess = Excess.toFixed(5)		
		BuySummary = "The transaction amount exceeds the balance by " + Excess + " " + SecondCurrency;
		$("#BuySummary").html(BuySummary);
		$("#BuySubmitButton").attr("disabled", "disabled");
		$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");
	}else{
		BuySummary = "The transaction amount " + GrandTotal  + " " + SecondCurrency;
		$("#BuySummary").html(BuySummary);
		$("#BuySubmitButton").removeAttr('disabled');
		$("#BuySubmitButton").attr("class", "btn btn-success btn-block");		
	}
	if(parseFloat(GrandTotal)===0){$("#BuySubmitButton").attr("disabled", "disabled");}
}
function SellFormCalculate (){
	BalanceFirst = $('#BalanceFirst').html();
	FirstCurrency = $('#SellFirstCurrency').val();
	SecondCurrency = $('#SellSecondCurrency').val();
	SellAmount = $('#SellAmount').val();
	SellPriceper = $('#SellPriceper').val();
	if(SellAmount=="" || SellAmount<=0){
		$("#SellSummary").html("Amount less than Zero!");
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");		
		return false;
	}
	if(SellAmount>9999){
		$("#SellSummary").html("Amount greater than 10000!");
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");		
		return false;
	}
	if(SellPriceper=="" || SellPriceper<=0){
		$("#SellSummary").html("Price less than Zero!");
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");		
		return false;
	}
	if(SellPriceper>9999){
		$("#SellSummary").html("Price greater than 10000!");
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");		
		return false;
	}

	TotalValue = SellAmount * SellPriceper;
	TotalValue = TotalValue.toFixed(6);
	$("#SellTotal").html(TotalValue);
	
	$.getJSON('/Updates/Commission/',
		function(ReturnValues){
			$("#SellCommission").val(ReturnValues['Commission']);			
			Commission = $('#SellCommission').val();;	
			Fees = TotalValue * Commission / 100;
			if(Fees<=0){return false;}
			Fees = Fees.toFixed(5);
			$("#SellFee").html(Fees);	
			$('#SellCommissionAmount').val(Fees);
			$('#SellCommissionCurrency').val(SecondCurrency);						
		}
	);

	GrandTotal = SellAmount;
	if(SellAmount==0){
	SellSummary = "Amount cannot be Zero";
		$("#SellSummary").html(SellSummary);
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");		
		return false;
	}

	if(parseFloat(BalanceFirst) < parseFloat(GrandTotal)){
		Excess =  parseFloat(GrandTotal) - parseFloat(BalanceFirst)  ;
		Excess = Excess.toFixed(5)
		SellSummary = "The transaction amount exceeds the balance by " + Excess + " " + FirstCurrency;
		$("#SellSummary").html(SellSummary);
		$("#SellSubmitButton").attr("disabled", "disabled");
		$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");				
	}else{
		SellSummary = "The transaction amount " + GrandTotal  + " " + FirstCurrency;
		$("#SellSummary").html(SellSummary);
		$("#SellSubmitButton").removeAttr('disabled');
		$("#SellSubmitButton").attr("class", "btn btn-success btn-block");				
	}
	if(parseFloat(GrandTotal)===0){$("#SellSubmitButton").attr("disabled", "disabled");}
}
function SellOrderFill(SellOrderPrice,SellOrderAmount){
	$("#BuyAmount").val(SellOrderAmount)  ;
	$("#BuyPriceper").val(SellOrderPrice)  ;
	$("#BuySubmitButton").attr("disabled", "disabled");	
	$("#BuySubmitButton").attr("class", "btn btn-warning btn-block");				
}
function BuyOrderFill(BuyOrderPrice,BuyOrderAmount){
	$("#SellAmount").val(BuyOrderAmount)  ;
	$("#SellPriceper").val(BuyOrderPrice)  ;
	$("#SellSubmitButton").attr("disabled", "disabled");	
	$("#SellSubmitButton").attr("class", "btn btn-warning btn-block");					
}
function ConvertBalance(){
	BTCRate = $("#BTCRate").val();
	LTCRate = $("#LTCRate").val();	
	USDRate = $("#USDRate").val();	
	GBPRate = $("#GBPRate").val();	
	EURRate = $("#EURRate").val();	
	Currency = $("#Currency" ).val();		
	switch(Currency){
		case "BTC":
		  break;
		case "LTC":
		  break;
		case "USD":
		  break;
		case "EUR":
		  break;
		case "GBP":
		  break;
	}
	
}

function SendPassword(){
	$.getJSON('/Users/SendPassword/'+$("#Username").val(),
		function(ReturnValues){
			if(ReturnValues['Password']=="Password Not sent"){
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");
				$("#LoginEmailPassword").hide();
				return false;
			}
			$("#LoginEmailPassword").show();
			$("#LoginButton").removeAttr('disabled');			
			$("#UserNameIcon").attr("class", "glyphicon glyphicon-ok");
			
			if(ReturnValues['TOTP']=="Yes"){
				$("#TOTPPassword").show();
			}else{
				$("#TOTPPassword").hide();
			}
//			if(ReturnValues['EmailPasswordSecurity']=="true"){
//				$("#LoginEmailPassword").show();
//			}else{
//				$("#LoginEmailPassword").hide();
//			}
			
		}
	);
}

function SaveTOTP(){
	if($("#ScannedCode").val()==""){return false;}
	$.getJSON('/Users/SaveTOTP/',{
			  Login:$("#Login").is(':checked'),
			  Withdrawal:$("#Withdrawal").is(':checked'),			  
			  Security:$("#Security").is(':checked'),
			  ScannedCode:$("#ScannedCode").val()
			  },
		function(ReturnValues){
			if(ReturnValues){
				window.location.assign("/users/settings");				
			}			
		}
	);
}
function CheckTOTP(){
	if($("#CheckCode").val()==""){return false;}
	$.getJSON('/Users/CheckTOTP/',{
			  CheckCode:$("#CheckCode").val()
			  },
		function(ReturnValues){
			if(ReturnValues){
				window.location.assign("/users/settings");		
			}
		}
	);
}


function DeleteTOTP(){
	$.getJSON('/Users/DeleteTOTP/',
		function(ReturnValues){}
	);	
}
function CheckDeposit(){
	AmountFiat = $("#AmountFiat").val();
	if(AmountFiat==""){return false;}
	}
function CheckWithdrawal(){
	if($("#WithdrawalMethod").val()=="bank"){
		AccountName = $("#AccountName").val();		
		if(AccountName==""){return false;}
		SortCode = $("#SortCode").val();
		if(SortCode==""){return false;}
		AccountNumber = $("#AccountNumber").val();
		if(AccountNumber==""){return false;}
	}
	if($("#WithdrawalMethod").val()=="bankBuss"){
		AccountName = $("#AccountName").val();		
		if(AccountName==""){return false;}
		SortCode = $("#SortCode").val();
		if(SortCode==""){return false;}
		AccountNumber = $("#AccountNumber").val();
		if(AccountNumber==""){return false;}
		CompanyName = $("#CompanyName").val();
		if(CompanyName==""){return false;}
		CompanyNumber = $("#CompanyNumber").val();
		if(CompanyNumber==""){return false;}
	}	
	if($("#WithdrawalMethod").val()=="post"){
		PostalName = $("#PostalName").val();
		if(PostalName==""){return false;}		
		PostalStreet = $("#PostalStreet").val();
		if(PostalStreet==""){return false;}		
		PostalAddress = $("#PostalAddress").val();
		if(PostalAddress==""){return false;}		
		PostalCity = $("#PostalCity").val();
		if(PostalCity==""){return false;}		
		PostalZip = $("#PostalZip").val();
		if(PostalZip==""){return false;}		
		PostalCountry = $("#PostalCountry").val();
		if(PostalCountry==""){return false;}		
	}
	WithdrawAmountFiat = $("#WithdrawAmountFiat").val();
	if(WithdrawAmountFiat==""){alert("Amount not entered");return false;}
	if(parseInt(WithdrawAmountFiat)<=5 && $("#WithdrawalMethod").val()!='okpay'){alert("Amount less than 5");return false;}
	if(parseInt(WithdrawAmountFiat)<1 && $("#WithdrawalMethod").val()=='okpay'){alert("Amount less than 1");return false;}	
}

function RejectReason(value){
	url = $("#RejectURL").attr('href');
	len = url.length-2;
	nurl = url.substr(0,len)+value;
	$("#RejectURL").attr('href',nurl);
}
function currencyAddress(currency){
	address = $("#currencyaddress").val();
  $("#Send"+currency+"Address").html(address); 	
	SuccessButtonDisable();
	}
function SuccessButtonDisable(){
	$("#SendSuccessButton").attr("disabled", "disabled");
}

function CheckCurrencyPayment(currency){
	
	address = $("#CurrencyAddress"+currency).val();
	if(address==""){return false;}
	
	$("#AmountError"+currency).hide();

	amount = $("#Amount"+currency).val();
	if(parseFloat(amount)<=0){
		$("#AmountError"+currency).show();
		return false;
	}
	if(amount==""){return false;}
	maxValue = $("#MaxValue"+currency).val();
	if(parseFloat(amount)==0){
		return false;}
	if(parseFloat(amount)>parseFloat(maxValue)){
		$("#AmountError"+currency).show();
		return false;
	}
	
	$.getJSON('/Updates/CurrencyAddress/'+currency+'/'+address,

		function(ReturnValues){
			if(ReturnValues['verify']['isvalid']!=true){
				switch(ReturnValues['currency'])
					{
					case "BTC":
					address = "<a href='http://blockchain.info/address/"+ address +"' target='_blank'>"+ address +"</a> <i class='glyphicon glyphicon-ok'></i>";
						break;
					case "XGC":
					address = "<a href='http://greencoin.io/blockchain/address/"+ address +"' target='_blank'>"+ address +"</a> <i class='glyphicon glyphicon-ok'></i>";

						break;
					case "TCP":
					address = address +" <i class='glyphicon glyphicon-ok'></i>";
						break;
					default:
					address = address +" <i class='glyphicon glyphicon-remove'></i>";					
					} 
					$("#Send"+currency+"SuccessButton").removeAttr('disabled');							
				}else{
						address = address +" <i class='glyphicon glyphicon-remove'></i>";					
				}
			$("#Send"+currency+"Address").html(address); 	
});
	return true;
	}
function PaymentMethod(value){
	if(value=="bank"){
		$("#WithdrawalBank").show();
		$("#WithdrawalBankBuss").hide();		
		$("#WithdrawalPost").hide();
		$("#WithdrawalOkPay").hide();		
	}
	if(value=="post"){
		$("#WithdrawalBank").hide();
		$("#WithdrawalBankBuss").hide();				
		$("#WithdrawalPost").show();
		$("#WithdrawalOkPay").hide();				
	}
	if(value=="bankBuss"){
		$("#WithdrawalBank").hide();
		$("#WithdrawalBankBuss").show();				
		$("#WithdrawalPost").hide();
		$("#WithdrawalOkPay").hide();				
	}
	if(value=="okpay"){
		$("#WithdrawalBank").hide();
		$("#WithdrawalBankBuss").hide();				
		$("#WithdrawalPost").hide();
		$("#WithdrawalOkPay").show();				
	}
}
function DepositByMethod(value){
	if(value=="okpay"){
		$("#DepositPost").hide();
		$("#DepositOkPay").show();		
		$("#MailSelect").hide();
		$("#OkPaySelect").show();		
	}
	if(value=="post"){
		$("#DepositPost").show();
		$("#DepositOkPay").hide();		
		$("#MailSelect").show();
		$("#OkPaySelect").hide();		
	}
}
function AutoFillBuy(){
	$("#BuyAmount").val($("#BalanceSecond").html());
}
function AutoFillSell(){
	$("#SellAmount").val($("#BalanceFirst").html());
}
function CheckFirstName(value){
	if(value.length>=2){
		$("#FirstNameIcon").attr("class", "glyphicon glyphicon-ok");	
	}else{
		$("#FirstNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckLastName(value){
	if(value.length>=2){
		$("#LastNameIcon").attr("class", "glyphicon glyphicon-ok");	
	}else{
		$("#LastNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckUserName(value){
	if(value.length>6){
		$.getJSON('/Users/username/'+value,
		function(ReturnValues){
			if(ReturnValues['Available']=='Yes'){
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-ok");	
			}else{
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");							
			}
		});
	}else{
		$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckEmail(email){
	email = email.toLowerCase();
	$("#Email").val(email);	
	if(validateEmail(email)){
		$.getJSON('/Users/signupemail/'+email,
			function(ReturnValues){
			if(ReturnValues['Available']=='Yes'){
				$("#EmailIcon").attr("class", "glyphicon glyphicon-ok");					

			}else{
				$("#EmailIcon").attr("class", "glyphicon glyphicon-remove");
			}
		});							
	}else{
		$("#EmailIcon").attr("class", "glyphicon glyphicon-remove");						
	}
}
function CheckPassword(value){
	if(value.length>6){
		if($("#Password").val()==$("#Password2").val()){
			$("#PasswordIcon").attr("class", "glyphicon glyphicon-ok");			
			$("#Password2Icon").attr("class", "glyphicon glyphicon-ok");					
		}else{
			$("#PasswordIcon").attr("class", "glyphicon glyphicon-remove");					
			$("#Password2Icon").attr("class", "glyphicon glyphicon-remove");							
		}
	}else{
		$("#PasswordIcon").attr("class", "glyphicon glyphicon-remove");					
		$("#Password2Icon").attr("class", "glyphicon glyphicon-remove");							
	}
	}

//	function EmailPasswordSecurity(value){
//		$.getJSON('/Users/EmailPasswordSecurity/'+value,
//		function(ReturnValues){});
//	}
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 
