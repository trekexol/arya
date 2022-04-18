

$("#account_bank").hide();
$("#account_efectivo").hide();
$("#credit_days").hide();
$("#reference").hide();
$("#account_punto_de_venta").hide();


$("#payment_type").on('change',function(){
    let inputPayment = document.getElementById("payment_type").value; 

    if(inputPayment == 1 || inputPayment == 11){
        //ACTIVACION DE CUENTAS BANCARIAS
        $("#account_bank").show();
        $("#credit_days").hide();
        $("#account_efectivo").hide();
        $("#reference").show();
        $("#account_punto_de_venta").hide();
        
    }else if(inputPayment == 4){
        //ACTIVACION DE DIAS DE CREDITO
        $("#account_bank").hide();
        $("#credit_days").show();
        $("#account_efectivo").hide();
        $("#reference").hide();
        $("#account_punto_de_venta").hide();

    }else if(inputPayment == 5){
        //ACTIVACION DE CUENTAS BANCARIAS
        $("#account_bank").show();
        $("#credit_days").hide();
        $("#account_efectivo").hide();
        $("#reference").show();
        $("#account_punto_de_venta").hide();

    }else if(inputPayment == 6){
        //ACTIVACION DE CUENTAS EFECTIVO
        $("#account_bank").hide();
        $("#credit_days").hide();
        $("#account_efectivo").show();
        $("#reference").show();
        $("#account_punto_de_venta").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){
        //ACTIVACION DE CUENTAS PUNTO DE VENTA
        $("#account_bank").hide();
        $("#credit_days").hide();
        $("#account_efectivo").hide();
        $("#reference").hide();
        $("#account_punto_de_venta").show();

    }else{
        $("#account_bank").hide();
        $("#credit_days").hide();
        $("#account_efectivo").hide();
        $("#reference").hide();
        $("#account_punto_de_venta").hide();
    }
});


$('#dataTable').DataTable({
"order": []
});





//AGREGAREMOS OTRO FORMULARIO DE PAGO
  
var number_form = 1; 
document.getElementById("amount_of_payments").value = number_form;

//AGREGAR FORMULARIOS
function addForm() {
    if(number_form < 7){
        number_form += 1; 
    }
    if(number_form == 2){
        $('#formulario2').show()
        document.getElementById("amount_pay2").value = "";
    }
    if(number_form == 3){
        $('#formulario3').show()
        document.getElementById("amount_pay3").value = "";
    }
    if(number_form == 4){
        $('#formulario4').show()
        document.getElementById("amount_pay4").value = "";
    }
    if(number_form == 5){
        $('#formulario5').show()
        document.getElementById("amount_pay5").value = "";
    }
    if(number_form == 6){
        $('#formulario6').show()
        document.getElementById("amount_pay6").value = "";
    }
    if(number_form == 7){
        $('#formulario7').show()
        document.getElementById("amount_pay7").value = "";
    }
        
    document.getElementById("amount_of_payments").value = number_form;
   
}


//AGREGAR FORMULARIOS
function deleteForm() {
    if(number_form <= 7){
        number_form -= 1; 
    }
    if(number_form == 1){
        $('#formulario2').hide()
        document.getElementById("amount_pay2").value = "";
    }
    if(number_form == 2){
        $('#formulario3').hide()
        document.getElementById("amount_pay3").value = "";
        
    }if(number_form == 3){
        $('#formulario4').hide()
        document.getElementById("amount_pay4").value = "";
        
    }if(number_form == 4){
        $('#formulario5').hide()
        document.getElementById("amount_pay5").value = "";
        
    }if(number_form == 5){
        $('#formulario6').hide()
        document.getElementById("amount_pay6").value = "";
        
    }if(number_form == 6){
        $('#formulario7').hide()
        document.getElementById("amount_pay7").value = "";
        
    }
    document.getElementById("amount_of_payments").value = number_form;
   
}

//Formulario 2
$("#formulario2").hide();

$("#account_bank2").hide();
$("#credit_days2").hide();
$("#account_efectivo2").hide();
$("#reference2").hide();
$("#account_punto_de_venta2").hide();

//------------------------

$("#payment_type2").on('change',function(){
    let inputPayment = document.getElementById("payment_type2").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank2").show();
        $("#credit_days2").hide();
        $("#account_efectivo2").hide();
        $("#reference2").show();
        $("#account_punto_de_venta2").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank2").hide();
        $("#credit_days2").show();
        $("#account_efectivo2").hide();
        $("#reference2").hide();
        $("#account_punto_de_venta2").hide();

    }else if(inputPayment == 5){

        $("#account_bank2").show();
        $("#credit_days2").hide();
        $("#account_efectivo2").hide();
        $("#reference2").show();
        $("#account_punto_de_venta2").hide();

    }else if(inputPayment == 6){

        $("#account_bank2").hide();
        $("#credit_days2").hide();
        $("#account_efectivo2").show();
        $("#reference2").hide();
        $("#account_punto_de_venta2").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank2").hide();
        $("#credit_days2").hide();
        $("#account_efectivo2").hide();
        $("#reference2").hide();
        $("#account_punto_de_venta2").show();

    }else{
        $("#account_bank2").hide();
        $("#credit_days2").hide();
        $("#account_efectivo2").hide();
        $("#reference2").hide();
        $("#account_punto_de_venta2").hide();
    }
});



//Formulario 3
$("#formulario3").hide();

$("#account_bank3").hide();
$("#credit_days3").hide();
$("#account_efectivo3").hide();
$("#reference3").hide();
$("#account_punto_de_venta3").hide();

//------------------------

$("#payment_type3").on('change',function(){
    let inputPayment = document.getElementById("payment_type3").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank3").show();
        $("#credit_days3").hide();
        $("#account_efectivo3").hide();
        $("#reference3").show();
        $("#account_punto_de_venta3").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank3").hide();
        $("#credit_days3").show();
        $("#account_efectivo3").hide();
        $("#reference3").hide();
        $("#account_punto_de_venta3").hide();

    }else if(inputPayment == 5){

        $("#account_bank3").show();
        $("#credit_days3").hide();
        $("#account_efectivo3").hide();
        $("#reference3").show();
        $("#account_punto_de_venta3").hide();

    }else if(inputPayment == 6){

        $("#account_bank3").hide();
        $("#credit_days3").hide();
        $("#account_efectivo3").show();
        $("#reference3").hide();
        $("#account_punto_de_venta3").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank3").hide();
        $("#credit_days3").hide();
        $("#account_efectivo3").hide();
        $("#reference3").hide();
        $("#account_punto_de_venta3").show();

    }else{
        $("#account_bank3").hide();
        $("#credit_days3").hide();
        $("#account_efectivo3").hide();
        $("#reference3").hide();
        $("#account_punto_de_venta3").hide();
    }
});



   //Formulario 4
$("#formulario4").hide();

$("#account_bank4").hide();
$("#credit_days4").hide();
$("#account_efectivo4").hide();
$("#reference4").hide();
$("#account_punto_de_venta4").hide();

//------------------------

$("#payment_type4").on('change',function(){
    let inputPayment = document.getElementById("payment_type4").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank4").show();
        $("#credit_days4").hide();
        $("#account_efectivo4").hide();
        $("#reference4").show();
        $("#account_punto_de_venta4").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank4").hide();
        $("#credit_days4").show();
        $("#account_efectivo4").hide();
        $("#reference4").hide();
        $("#account_punto_de_venta4").hide();

    }else if(inputPayment == 5){

        $("#account_bank4").show();
        $("#credit_days4").hide();
        $("#account_efectivo4").hide();
        $("#reference4").show();
        $("#account_punto_de_venta4").hide();

    }else if(inputPayment == 6){

        $("#account_bank4").hide();
        $("#credit_days4").hide();
        $("#account_efectivo4").show();
        $("#reference4").hide();
        $("#account_punto_de_venta4").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank4").hide();
        $("#credit_days4").hide();
        $("#account_efectivo4").hide();
        $("#reference4").hide();
        $("#account_punto_de_venta4").show();

    }else{
        $("#account_bank4").hide();
        $("#credit_days4").hide();
        $("#account_efectivo4").hide();
        $("#reference4").hide();
        $("#account_punto_de_venta4").hide();
    }
});


 //Formulario 5
$("#formulario5").hide();

$("#account_bank5").hide();
$("#credit_days5").hide();
$("#account_efectivo5").hide();
$("#reference5").hide();
$("#account_punto_de_venta5").hide();

//------------------------

$("#payment_type5").on('change',function(){
    let inputPayment = document.getElementById("payment_type5").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank5").show();
        $("#credit_days5").hide();
        $("#account_efectivo5").hide();
        $("#reference5").show();
        $("#account_punto_de_venta5").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank5").hide();
        $("#credit_days5").show();
        $("#account_efectivo5").hide();
        $("#reference5").hide();
        $("#account_punto_de_venta5").hide();

    }else if(inputPayment == 5){

        $("#account_bank5").show();
        $("#credit_days5").hide();
        $("#account_efectivo5").hide();
        $("#reference5").show();
        $("#account_punto_de_venta5").hide();

    }else if(inputPayment == 6){

        $("#account_bank5").hide();
        $("#credit_days5").hide();
        $("#account_efectivo5").show();
        $("#reference5").hide();
        $("#account_punto_de_venta5").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank5").hide();
        $("#credit_days5").hide();
        $("#account_efectivo5").hide();
        $("#reference5").hide();
        $("#account_punto_de_venta5").show();

    }else{
        $("#account_bank5").hide();
        $("#credit_days5").hide();
        $("#account_efectivo5").hide();
        $("#reference5").hide();
        $("#account_punto_de_venta5").hide();
    }
});

 //Formulario 6
$("#formulario6").hide();

$("#account_bank6").hide();
$("#credit_days6").hide();
$("#account_efectivo6").hide();
$("#reference6").hide();
$("#account_punto_de_venta6").hide();

//------------------------

$("#payment_type6").on('change',function(){
    let inputPayment = document.getElementById("payment_type6").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank6").show();
        $("#credit_days6").hide();
        $("#account_efectivo6").hide();
        $("#reference6").show();
        $("#account_punto_de_venta6").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank6").hide();
        $("#credit_days6").show();
        $("#account_efectivo6").hide();
        $("#reference6").hide();
        $("#account_punto_de_venta6").hide();

    }else if(inputPayment == 5){

        $("#account_bank6").show();
        $("#credit_days6").hide();
        $("#account_efectivo6").hide();
        $("#reference6").show();
        $("#account_punto_de_venta6").hide();

    }else if(inputPayment == 6){

        $("#account_bank6").hide();
        $("#credit_days6").hide();
        $("#account_efectivo6").show();
        $("#reference6").hide();
        $("#account_punto_de_venta6").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank6").hide();
        $("#credit_days6").hide();
        $("#account_efectivo6").hide();
        $("#reference6").hide();
        $("#account_punto_de_venta6").show();

    }else{
        $("#account_bank6").hide();
        $("#credit_days6").hide();
        $("#account_efectivo6").hide();
        $("#reference6").hide();
        $("#account_punto_de_venta6").hide();
    }
});

 //Formulario 7
$("#formulario7").hide();

$("#account_bank7").hide();
$("#credit_days7").hide();
$("#account_efectivo7").hide();
$("#reference7").hide();
$("#account_punto_de_venta7").hide();

//------------------------

$("#payment_type7").on('change',function(){
    let inputPayment = document.getElementById("payment_type7").value; 

    if(inputPayment == 1 || inputPayment == 11){

        $("#account_bank7").show();
        $("#credit_days7").hide();
        $("#account_efectivo7").hide();
        $("#reference7").show();
        $("#account_punto_de_venta7").hide();
        
    }else if(inputPayment == 4){

        $("#account_bank7").hide();
        $("#credit_days7").show();
        $("#account_efectivo7").hide();
        $("#reference7").hide();
        $("#account_punto_de_venta7").hide();

    }else if(inputPayment == 5){

        $("#account_bank7").show();
        $("#credit_days7").hide();
        $("#account_efectivo7").hide();
        $("#reference7").show();
        $("#account_punto_de_venta7").hide();

    }else if(inputPayment == 6){

        $("#account_bank7").hide();
        $("#credit_days7").hide();
        $("#account_efectivo7").show();
        $("#reference7").hide();
        $("#account_punto_de_venta7").hide();

    }else if((inputPayment == 9) || (inputPayment == 10)){

        $("#account_bank7").hide();
        $("#credit_days7").hide();
        $("#account_efectivo7").hide();
        $("#reference7").hide();
        $("#account_punto_de_venta7").show();

    }else{
        $("#account_bank7").hide();
        $("#credit_days7").hide();
        $("#account_efectivo7").hide();
        $("#reference7").hide();
        $("#account_punto_de_venta7").hide();
    }
});











$(document).ready(function () {
    $("#anticipo").mask('000.000.000.000.000,00', { reverse: true });
});

$(document).ready(function () {
    $("#amount_pay").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay2").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay3").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay4").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay5").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay6").mask('000.000.000.000.000,00', { reverse: true });
});
$(document).ready(function () {
    $("#amount_pay7").mask('000.000.000.000.000,00', { reverse: true });
});

$(document).ready(function () {
    $("#amount_dolar").mask('000.000.000.000.000,00', { reverse: true });
});


$(document).ready(function () {
    $("#credit_days").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days2").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days3").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days4").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days5").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days6").mask('00000000', { reverse: true });
});
$(document).ready(function () {
    $("#credit_days7").mask('00000000', { reverse: true });
});






