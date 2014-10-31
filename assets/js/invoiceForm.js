/**
* InvoiceForm - Editable invoice generator
* @author Adriaan Ebbeling
* @version 1.0
*/

// from http://www.mediacollege.com/internet/javascript/number/round.html
function roundNumber(number,decimals) {
  var newString;// The new rounded number
  decimals = Number(decimals);
  if (decimals < 1) {
    newString = (Math.round(number)).toString();
  } else {
    var numString = number.toString();
    if (numString.lastIndexOf(".") == -1) {// If there is no decimal point
      numString += ".";// give it one at the end
    }
    var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we'll end up with
    var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    if (d2 >= 5) {// Do we need to round up at all? If not, the string will just be truncated
      if (d1 == 9 && cutoff > 0) {// If the last digit is 9, find a new cutoff point
        while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          if (d1 != ".") {
            cutoff -= 1;
            d1 = Number(numString.substring(cutoff,cutoff+1));
          } else {
            cutoff -= 1;
          }
        }
      }
      d1 += 1;
    } 
    if (d1 == 10) {
      numString = numString.substring(0, numString.lastIndexOf("."));
      var roundedNum = Number(numString) + 1;
      newString = roundedNum.toString() + '.';
    } else {
      newString = numString.substring(0,cutoff) + d1.toString();
    }
  }
  if (newString.lastIndexOf(".") == -1) {// Do this again, to the new string
    newString += ".";
  }
  var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  for(var i=0;i<decimals-decs;i++) newString += "0";
  //var newNumber = Number(newString);// make it a number if you like
  return newString; // Output the result to the form field (change for your purposes)
}

// Show the selected logo image
function logoImg(input) {
  if (input.files && input.files[0]) {
  var reader = new FileReader();

  reader.onload = function (e) {
    $('#img_prev')
    .attr('src', e.target.result);
  };

  reader.readAsDataURL(input.files[0]);
}
$('.delete-logo').css('display', 'inline-block');
}
    
// Update total invoice amount
function update_total() {
  var total = 0;
  var taxrate = $('#invoice_taxrate').val();
  var totalamount = 0;
  
  $('.price').each(function(i){
    price = $(this).html().replace("$","");
    if (!isNaN(price)) total += Number(price);
  });
  
  subtotal = roundNumber(total,2);
  taxtotal = roundNumber(subtotal * taxrate - subtotal,2);
  total = roundNumber(subtotal * taxrate,2);
  
  $('#subtotal').html("$"+subtotal);
  $('#taxtotal').html("$"+taxtotal);
  $('#invoice_total_tax').val(taxtotal);
  $('#total').html("$"+total);
  
  update_balance();
}

// Update total balance
function update_balance() {
  var due = $("#total").html().replace("$","") - $("#paid").val().replace("$","");
  due = roundNumber(due,2);
  
  $('.due').html("$"+due);
}

// Update prices
function update_price() {
  var row = $(this).parents('.item-row');
  var price = row.find('.cost').val().replace("$","") * row.find('.qty').val();
  price = roundNumber(price,2);
  isNaN(price) ? row.find('.price').html("N/A") : row.find('.price').html("$"+price);
  
  update_total();
}

function bind() {
  $(".cost").blur(update_price);
  $(".qty").blur(update_price);

}

$(document).ready(function() {
    
  // Activate the bootstrap datepicker
  $('.datepicker').datepicker({
    format: 'mm/dd/yyyy',
    autoclose: true
  })
  
  // Delete the invoice logo 
  $( "#delete-logo" ).click(function() {
    $('#img_prev').attr('src', 'assets/img/logo-placeholder.png'); // change to your default logo src 
    $('.select-logo').val('');
    $('#delete-logo').css('display', 'none');
  });
  
  // Activate validation for all file inputs
  $.validate({
    modules : 'file'
  });

  $('input').click(function(){
    $(this).select();
  });

  $("#paid").blur(update_balance);
    $("#invoice_taxrate").change(update_total);
  
  // Add new items row to invoice
  // If you like to change your HTML for your invoice item, do so below, make sure you keep the right classes
  $("#addrow").click(function(){
    $(".item-row:last").after('<tr class="item-row"><td class="item-name"><div class="delete-wpr"><input type="text" class="form-control" placeholder="Item name" name="item_name[]" value=""><a class="delete btn" href="javascript:;" title="Remove row"><span class="glyphicon glyphicon-remove-circle" style="color:#cccccc;"></span></a></div></td><td class="description"><textarea class="form-control" name="item_description[]" rows="1" placeholder="Item description"></textarea></td><td><div class="input-group input-group"><span class="input-group-addon">$</span><input type="text" class="cost form-control" name="item_price[]" placeholder="0.00"></div></td><td><input type="text" class="qty form-control" value="" name="item_qty[]" placeholder="0"></td><td align="right"><span class="price">$0.00</span></td></tr>');
    if ($(".delete").length > 0) $(".delete").show();
    bind();
  });
  
  bind();
  
  $(document).on("click","a.delete", function() {
    $(this).parents('.item-row').remove();
    update_total();
    if ($(".delete").length < 2) $(".delete").hide();
  });
  

});