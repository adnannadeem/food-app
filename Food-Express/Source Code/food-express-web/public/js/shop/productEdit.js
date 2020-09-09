/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */
"use strict";

$('#single-product').hide();
$('#product-variation').hide();
$('#product-option').hide();

if(get_product_type == 5) {
    $('#single-product').show('slow');
    $('#product-variation').hide('slow');
    $('#product-option').show('slow');
} else if(get_product_type == 10) {
    $('#single-product').hide('slow');
    $('#product-variation').show('slow');
    $('#product-option').show('slow');
} else {
    $('#single-product').hide('slow');
    $('#product-variation').hide('slow');
    $('#product-option').hide('slow');
}

$('#product_type').on('change', function() {
    var  product_type = $(this).val();
    if(product_type == 5) {
        $('#single-product').show('slow');
        $('#product-variation').hide('slow');
        $('#product-option').show('slow');
    } else if(product_type == 10) {
        $('#single-product').hide('slow');
        $('#product-variation').show('slow');
        $('#product-option').show('slow');
    } else {
        $('#single-product').hide('slow');
        $('#product-variation').hide('slow');
        $('#product-option').hide('slow');
    }
});

if(product_type == 5) {
    $('#single-product').show();
    $('#product-option').show();
}

if(product_type == 10) {
    $('#product-variation').show();
    $('#product-option').show();
}

function variationItemDesign() {
    product_variation_count++;
    var markup = '';
    markup += '<tr>';
        markup += '<td>';
            markup += '<input type="text" name="variation['+product_variation_count+'][name]" placeholder="Name" name="name" class="form-control form-control-sm">';
        markup +='</td>';
        markup +='<td>';
            markup += '<input type="number" name="variation['+product_variation_count+'][price]" placeholder="Price" class="form-control form-control-sm change-productprice">';
        markup += '</td>';
        markup +='<td>';
            markup +='<input type="number" name="variation['+product_variation_count+'][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity">';
        markup +='</td>';
        markup +='<td>';
            markup += '<button class="btn btn-danger btn-sm removeBtn">'
                markup += '<i class="fa fa-trash"></i>';
            markup += '</button>';
        markup += '</td>';
    markup += '</tr>';
    return markup;
}

function optionItemDesign() {
    product_option_count++;
    var markup = '';
    markup += '<tr>';
        markup += '<td>';
            markup += '<input type="text" name="option['+product_option_count+'][name]" placeholder="Name" class="form-control form-control-sm">';
        markup +='</td>';
        markup +='<td>';
            markup += '<input type="number" name="option['+product_option_count+'][price]" placeholder="Price" class="form-control form-control-sm change-productprice">';
        markup += '</td>';
        markup +='<td>';
            markup += '<button class="btn btn-danger btn-sm removeBtn">'
                markup += '<i class="fa fa-trash"></i>';
            markup += '</button>';
        markup += '</td>';
    markup += '</tr>';
    return markup;
}

$('#variation-add').on('click', function(event) {
    event.preventDefault();
    $(this).parent().parent().parent().prepend(variationItemDesign());
});

$('#option-add').on('click', function(event) {
    event.preventDefault();
    $(this).parent().parent().parent().prepend(optionItemDesign());
});

$(document).on('click','.removeBtn', function(event) {
    event.preventDefault();
    $(this).parent().parent().remove()
});

$(document).on('keyup', '.change-productprice', function() {
    var productPrice =  toFixedVal($(this).val());
    $(this).val(productPrice);

    if(dotAndNumber(productPrice)) {
        if(productPrice.length > 15) {
            productPrice = lenChecker(productPrice, 15);
            $(this).val(productPrice);
        }

        if(productPrice != '' && productPrice != null) {
            if(floatChecker(productPrice)) {
                if(productPrice.length > 15) {
                    productPrice = lenChecker(productPrice, 15);
                    $(this).val(productPrice);
                }
            }
        }
    } else {
        var productPrice = parseSentenceForNumber(toFixedVal($(this).val()));
        $(this).val(productPrice);
    }
});

$(document).on('keyup', '.change-productquantity', function() {
    var productQuantity = $(this).val();
    var productQuantity = Math.trunc(productQuantity);
    $(this).val(productQuantity);
});

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function floatChecker(value) {
    var val = value;
    if(isNumeric(val)) {
        return true;
    } else {
        return false;
    }
}

function dotAndNumber(data) {
    var retArray = [];
    var fltFlag = true;
    if(data.length > 0) {
        for(var i = 0; i <= (data.length-1); i++) {
            if(i == 0 && data.charAt(i) == '.') {
                fltFlag = false;
                retArray.push(true);
            } else {
                if(data.charAt(i) == '.' && fltFlag == true) {
                    retArray.push(true);
                    fltFlag = false;
                } else {
                    if(isNumeric(data.charAt(i))) {
                        retArray.push(true);
                    } else {
                        retArray.push(false);
                    }
                }

            }
        }
    }

    if(jQuery.inArray(false, retArray) ==  -1) {
        return true;
    }
    return false;
}

function toFixedVal(x) {
  if (Math.abs(x) < 1.0) {
    var e = parseFloat(x.toString().split('e-')[1]);
    if (e) {
        x *= Math.pow(10,e-1);
        x = '0.' + (new Array(e)).join('0') + x.toString().substring(2);
    }
  } else {
    var e = parseFloat(x.toString().split('+')[1]);
    if (e > 20) {
        e -= 20;
        x /= Math.pow(10,e);
        x += (new Array(e+1)).join('0');
    }
  }
  return x;
}

function parseSentenceForNumber(sentence) {
    var matches = sentence.replace(/,/g, '').match(/(\+|-)?((\d+(\.\d+)?)|(\.\d+))/);
    return matches && matches[0] || null;
}

function lenChecker(data, len) {
    var retdata = 0;
    var lencount = 0;
    data = toFixedVal(data);
    if(data.length > len) {
        lencount = (data.length - len);
        data = data.toString();
        data = data.slice(0, -lencount);
        retdata = parseFloat(data);
    } else {
        retdata = parseFloat(data);
    }

    return toFixedVal(retdata);
}