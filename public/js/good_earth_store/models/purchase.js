steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Purchase
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend purchase services.
 */
$.Model('GoodEarthStore.Models.Purchase',
/* @Static */
{

	process:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;


		var errors=this.validate(data.cardData);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

var outOrders=[];

	var list=data.purchase.orders;
	for (var i=0, len=list.length; i<len; i++){
		var element=list[i];
		outOrders.push({
			day:{refId:element.day.refId},
			offer:{refId:element.offering.refId},
			student:{refId:element.student.refId},
			refId:element.refId

		});
	}

var outObj={};
for (var i in data){
	if (i=='purchase'){continue;} //skip this
	outObj[i]=data[i];
}

outObj['orders']=outOrders;
outObj['refId']=data.purchase.refId;
		$.ajax({
				url: '/purchase/pay',
				type: 'post',
				dataType: 'json',
				data: {data:outObj},
				success: success,
				error: error,
				fixture: false
			});

},
validate:function(inData){
	var name, datum,
		errors=[];
	name='cardName';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Name is required"]);}
	name='billingStreet';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Street is required"]);}
	name='billingCity';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "City is required"]);}
	name='billingState';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "State is required"]);}
	name='billingZip';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Zip Code is required"]);}
	name='phoneNumber';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Phone Number is required"]);}
	name='cardNumber';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Card Number is required"]);}
	name='expirationDate';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Expiration Date is required"]);}
	name='ccv';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Security Code is required"]);}

	return errors;
}


},
/* @Prototype */
{});

})