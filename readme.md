This extension adds a [Beanstream](http://www.beanstream.com) payment gateway to [Expresso Store](https://exp-resso.com) 2.5.0 and greater for [ExpressionEngine®](http://ellislab.com/expressionengine).

## Features

- Support for both **Authorize Only** and **Purchase (Authorize and Capture)** settings in Expresso Store.
- Support for processing refunds directly from the order administration screen.
- Support for Interac Online payments (Canadian merchants only).
- Support for using Beanstream's Legato.js library, which allows you to process payments *without* the customer's credit card number, CVD, and expiry date being posted to your server, reducing your PCI compliance needs.

## Installation

Third-party payment gateways are added to Expresso Store by activating the add-on's extension. Beanstream Payment Gateway for Store also includes a module which must be installed alongside the extension in order to support Interac Online payments and the Legato.js tokenization process.

Simply upload the `store_beanstream` folder to your `/system/expressionengine/third_party` folder, then activate both the module and extension.

## Configuration

Access the gateway settings screen by going to **Add-ons &rarr; Moudles &rarr; Store &rarr; Settings &rarr; Payment Methods**, then clicking on **Beanstream**.

You will need to enter both your **Merchant ID** and **API Passcode**, which can be obtained via your Beanstream control panel, or by contacting Beanstream support. If you have a sandbox account with Beanstream, you can also enter both of these values for your sandbox account in their corresponding "test" inputs and put the gateway into "Test" mode.

The gateway settings screen also displays a URL which is to be used for your Funded/Non-Funded URL when setting up Interac Online payments. This value cannot be changed, and it must be provided to Beanstream as-is in order for Interac Online payments to be activated and function correctly.

## Accepting Interac Online payments

In addition to providing the Funded/Non-Funded URL (noted above) to Beanstream, there are some additional steps required in order to accept Interac Online payments.

### Selecting Interac Online vs credit card payment

On the final stage of your checkout process, the customer must be given a choice of which Beanstream payment type to use via a `select` or `radio` input. This input must be named `beanstream_payment_type`, and the value for Interac Online payments must be `interac`. If no `beanstream_payment_type` is posted, the payment is assumed to be a credit card payment.

    <select id="payment_type" name="beanstream_payment_type">
        <option value="card">Credit Card</option>
        <option value="interac">Interac Online</option>
    </select>

(You may want to include some javascript which dynamically hides the credit card number, expiration date, and CVC inputs when Interac Online is selected. See example code below.)

### Setting the language of the Interac Online interface

By default, the Interac Online process flow will be displayed to the customer in English, but you may post a variable named `beanstream_language` in your checkout form to set it to `fra` for French.

### Displaying required Interac Online response data after checkout

Interac Online has several display requirements which must be met before a customer begins and after a customer completes a transaction. (You should familiarize yourself with these requirement and ensure your templates reflect them.) Store does not have methods in place to display some of this data after checkout, so Beanstream Payment Gateway for Store has its own methods for saving and displaying it in your template.

Inside of your `{exp:store:orders}` tag, where you display the customer's order confirmation or order record, include the following tag (using whatever formatting and placement you wish):

    {exp:store_beanstream:interac_response order_hash="{segment_3}"}
        <b>Institution:</b> {interac_institution_name}<br />
        <b>Confirmation Code:</b> {interac_institution_confirmation_code}<br />
        <b>Authorization Code:</b> {interac_auth_code}
    {/exp:store_beanstream:interac_response}

(Note that the segment you pass to the `order_hash` parameter may vary depending on your template/routing setup.)

## Using Legato.js to tokenize credit cards

This gateway supports posting a valid Legato.js token in place of credit card number, card expiration, and CVC data. You may use the [example code offered in the official documentation](http://developer.beanstream.com/documentation/legato/javascript-library/), or modify it to suit your individual site and needs. (For example, if you wish to still post credit card details when visitors with javascript disabled attempt to checkout, rather than preventing them from completing their purchase.)

Two template changes are required in order to successfully use Legato.js in your checkout form.

### Card expiration year menu

You must replace Store's `{exp_year_options}` variable with the `{exp:store_beanstream:year_options}` tag. This is a requirement because Beanstream expects the card expiration year be in 2-digit format, whereas Store uses a 4-digit year format. (When the expiration year is posted to the server it can be shortened to become compatible with Beanstream's API, but this can't be done when using Legato.js.)

### Credit card input IDs

Legato.js uses hard-coded input IDs in order to compile the credit card data and fetch a payment token, so your template must be updated to reflect these IDs. These IDs are `trnCardNumber`, `trnExpMonth`,
`trnExpYear`, and `trnCardCvd`.

    <input type="text" id="trnCardNumber" value="" />
    <select id="trnExpMonth">
        <option value=""></option>
        {exp_month_options}
    </select>
    <select id="trnExpYear">
        <option value=""></option>
        {exp:store_beanstream:year_options}
    </select>
    <input type="text" id="trnCardCvd" value="" />

## Example code

This example shows how a very simple payment form, with accompanying javascript, might look when incorporating all of the gateway's features.

	<!-- Include the Legato.js library. This assumes you have already loaded jQuery into your template, as it is a required dependency. -->
	<script src="//www.beanstream.com/scripts/tokenization/legato-1.1.min.js"></script>
	<!--[if IE]>
		<script src="//www.beanstream.com/scripts/tokenization/json2.min.js"></script>
	<![endif]-->
	<script>
	$(document).ready(function()
	{
		// Show/hide the credit card fields based on the type of payment selected
		$('#payment_type').on('change', function()
		{
			if($(this).val() == 'interac')
			{
				$('#creditCardFields').slideUp();
			}
			else
			{
				$('#creditCardFields').slideDown();
			}
		});
	
		$('#checkoutFormSubmit').click(function(e)
		{
			// Only fetch the legato token if the payment type is not interac
			if($('#payment_type').val() != 'interac')
			{
				// Prevent the form from being submitted by this click
				e.preventDefault();
				
				// Disable the form while we fetch the token to prevent duplicate submissions
				$('#checkoutFormSubmit').attr("disabled", "disabled");

				getLegato(function (legato) {
					if (legato.success) {
						
						// Remove the "name" attributes to prevent card data from being posted
						$("#trnCardNumber, #trnExpMonth, #trnExpYear, #trnCardCvd").removeAttr('name');
						
						// Add the Legato token to the form
						$("#checkoutForm").append($('<input type="hidden" name="legatoToken"/>').val(legato.token));
						
						// Proceed with checkout
						$("#checkoutForm").submit();
						
					} else {
						
						// This error message could also be added to the DOM in an appropriate location
						alert(legato.message);
						
						// Re-enable the form
						$('#checkoutFormSubmit').removeAttr("disabled");
					}
			   });
			}
		});
	});
	</script>

	{exp:store:checkout form_id="checkoutForm" payment_method="beanstream" return="store/order/ORDER_HASH" required=""}
	<fieldset>

		<legend>Payment Method</legend>
		{error:payment_method}

		<label for="payment_type">Type</label>
		<select id="payment_type" name="beanstream_payment_type">
			<option value="card">Credit Card</option>
			<option value="interac">Interac Online</option>
		</select>

		<div id="creditCardFields">
	
			<label for="trnCardNumber">Card Number</label>
			<input type="text" id="trnCardNumber" name="payment[card_no]" value="" />

			<label for="payment_name">Name</label>
			<input type="text" id="payment_name" name="payment[name]" value="" />

			<label for="trnExpMonth">Expiry</label>
			<select id="trnExpMonth" name="payment[exp_month]" style="width:auto">
				<option value=""></option>
				{exp_month_options}
			</select>
		
			<select id="trnExpYear" name="payment[exp_year]" >
				<option value=""></option>
				{exp:store_beanstream:year_options}
			</select>

			<label for="trnCardCvd">CSC</label>
			<input type="text" id="trnCardCvd" name="payment[csc]" value="" />

		</div>

		<input type="hidden" name="beanstream_language" value="eng" />
		<input type="hidden" name="commit" value="y" />
		<input type="submit" id="checkoutFormSubmit" value="Place Order" />

	</fieldset>
	{/exp:store:checkout}