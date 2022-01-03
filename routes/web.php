<?php



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});
Route::get('about', function () {
    return view('about');
});

Route::get('contact', function () {
    return view('contact');
});

Route::post('asignacnioasd','UserController@assigndatabase')->name('assigndatabase');




Auth::routes();

Route::get('/home/{coin?}', 'BackendController@index')->name('home');

Route::group(["prefix"=>'users'],function(){
    Route::get('/','UserController@index')->name('users');
    Route::get('register','UserController@create')->name('users.create');
    Route::post('store', 'UserController@store')->name('users.store');

    Route::get('{id}/edit','UserController@edit')->name('users.edit');
    Route::delete('delete','UserController@destroy')->name('users.delete');
    Route::patch('{id}/update','UserController@update')->name('users.update');

    Route::get('createassignmodules/{id_user}','UserController@createAssignModules')->name('users.createAssignModules');
    Route::post('assignmodules', 'UserController@assignModules')->name('users.assignModules');

});


Route::group(["prefix"=>'salarytypes'],function(){
    Route::get('/','SalarytypeController@index')->name('salarytypes');
    Route::get('register','SalarytypeController@create')->name('salarytypes.create');
    Route::post('store','SalarytypeController@store')->name('salarytypes.store');

    Route::get('{id}/edit','SalarytypeController@edit')->name('salarytypes.edit');
    Route::delete('{id}/delete','SalarytypeController@destroy')->name('salarytypes.delete');
    Route::patch('{id}/update','SalarytypeController@update')->name('salarytypes.update');

});


Route::group(["prefix"=>'positions'],function(){
    Route::get('/','PositionsController@index')->name('positions');
    Route::get('register','PositionsController@create')->name('positions.create');
    Route::post('store','PositionsController@store')->name('positions.store');

    Route::get('{id}/edit','PositionsController@edit')->name('positions.edit');
    Route::delete('{id}/delete','PositionsController@destroy')->name('positions.delete');
    Route::patch('{id}/update','PositionsController@update')->name('positions.update');

});

Route::group(["prefix"=>'academiclevels'],function(){
    Route::get('/','AcademiclevelsController@index')->name('academiclevels');
    Route::get('register','AcademiclevelsController@create')->name('academiclevels.create');
    Route::post('store','AcademiclevelsController@store')->name('academiclevels.store');

    Route::get('{id}/edit','AcademiclevelsController@edit')->name('academiclevels.edit');
    Route::delete('{id}/delete','AcademiclevelsController@destroy')->name('academiclevels.delete');
    Route::patch('{id}/update','AcademiclevelsController@update')->name('academiclevels.update');

});


Route::group(["prefix"=>'professions'],function(){
    Route::get('/','ProfessionsController@index')->name('professions');
    Route::get('register','ProfessionsController@create')->name('professions.create');
    Route::post('store','ProfessionsController@store')->name('professions.store');

    Route::get('{id}/edit','ProfessionsController@edit')->name('professions.edit');
    Route::delete('{id}/delete','ProfessionsController@destroy')->name('professions.delete');
    Route::patch('{id}/update','ProfessionsController@update')->name('professions.update');

});

Route::group(["prefix"=>'employees'],function(){
    Route::get('/','EmployeeController@index')->name('employees');
    Route::get('register','EmployeeController@create')->name('employees.create');
    Route::post('store', 'EmployeeController@store')->name('employees.store');

    Route::get('{id}/edit','EmployeeController@edit')->name('employees.edit');
    Route::delete('{id}/delete','EmployeeController@destroy')->name('employees.delete');
    Route::patch('{id}/update','EmployeeController@update')->name('employees.update');

});


/* CONSULTAS PARA SELECT DEPENDIENTES ESTADO--MUNICIPIO--PARRQUIA */
Route::group(["prefix"=>"municipio"],function(){
    Route::get('/','MunicipioController@index')->name('municipio.index');
    Route::get('list/{estado_id?}','MunicipioController@list')->name('municipio.list');
});
Route::group(["prefix"=>"parroquia"],function(){
    Route::get('/','ParroquiaController@index')->name('parroquia.index');
    Route::get('list/{municipio_id?}/{estado_id?}','ParroquiaController@list')->name('parroquia.list');
});

/* BOTON CANCELAR */
Route::get('danger/{ruta}', function($ruta) {
    return redirect()->route($ruta)->with('danger','Acción Cancelada!');
})->name('danger');



Route::group(["prefix"=>'segments'],function(){
    Route::get('/','SegmentController@index')->name('segments');
    Route::get('register','SegmentController@create')->name('segments.create');
    Route::post('store', 'SegmentController@store')->name('segments.store');

    Route::get('{id}/edit','SegmentController@edit')->name('segments.edit');
    Route::delete('{id}/delete','SegmentController@destroy')->name('segments.delete');
    Route::patch('{id}/update','SegmentController@update')->name('segments.update');

});
Route::group(["prefix"=>'subsegment'],function(){
    Route::get('/','SubsegmentController@index')->name('subsegment');
    Route::get('register','SubsegmentController@create')->name('subsegment.create');
    Route::post('store', 'SubsegmentController@store')->name('subsegment.store');

    Route::get('{id}/edit','SubsegmentController@edit')->name('subsegment.edit');
    Route::delete('{id}/delete','SubsegmentController@destroy')->name('subsegment.delete');
    Route::patch('{id}/update','SubsegmentController@update')->name('subsegment.update');


    Route::get('list/{estado_id?}','SubsegmentController@list')->name('subsegment.list');

});
Route::group(["prefix"=>'unitofmeasures'],function(){
    Route::get('/','UnitOfMeasureController@index')->name('unitofmeasures');
    Route::get('register','UnitOfMeasureController@create')->name('unitofmeasures.create');
    Route::post('store','UnitOfMeasureController@store')->name('unitofmeasures.store');

    Route::get('{id}/edit','UnitOfMeasureController@edit')->name('unitofmeasures.edit');
    Route::delete('{id}/delete','UnitOfMeasureController@destroy')->name('unitofmeasures.delete');
    Route::patch('{id}/update','UnitOfMeasureController@update')->name('unitofmeasures.update');

});

Route::group(["prefix"=>'clients'],function(){
    Route::get('/','ClientController@index')->name('clients');
    Route::get('register','ClientController@create')->name('clients.create');
    Route::post('store','ClientController@store')->name('clients.store');

    Route::get('{id}/edit','ClientController@edit')->name('clients.edit');
    Route::delete('{id}/delete','ClientController@destroy')->name('clients.delete');
    Route::patch('{id}/update','ClientController@update')->name('clients.update');

});

Route::group(["prefix"=>'providers'],function(){
    Route::get('/','ProviderController@index')->name('providers');
    Route::get('register','ProviderController@create')->name('providers.create');
    Route::post('store','ProviderController@store')->name('providers.store');

    Route::get('{id}/edit','ProviderController@edit')->name('providers.edit');
    Route::delete('{id}/delete','ProviderController@destroy')->name('providers.delete');
    Route::patch('{id}/update','ProviderController@update')->name('providers.update');

});

Route::group(["prefix"=>'branches'],function(){
    Route::get('/','BranchController@index')->name('branches');
    Route::get('register','BranchController@create')->name('branches.create');
    Route::post('store','BranchController@store')->name('branches.store');

    Route::get('{id}/edit','BranchController@edit')->name('branches.edit');
    Route::delete('{id}/delete','BranchController@destroy')->name('branches.delete');
    Route::patch('{id}/update','BranchController@update')->name('branches.update');

});

Route::group(["prefix"=>'nominatypes'],function(){
    Route::get('/','NominaTypeController@index')->name('nominatypes');
    Route::get('register','NominaTypeController@create')->name('nominatypes.create');
    Route::post('store','NominaTypeController@store')->name('nominatypes.store');

    Route::get('{id}/edit','NominaTypeController@edit')->name('nominatypes.edit');
    Route::delete('{id}/delete','NominaTypeController@destroy')->name('nominatypes.delete');
    Route::patch('{id}/update','NominaTypeController@update')->name('nominatypes.update');

});

Route::group(["prefix"=>'paymenttypes'],function(){
    Route::get('/','PaymentTypeController@index')->name('paymenttypes');
    Route::get('register','PaymentTypeController@create')->name('paymenttypes.create');
    Route::post('store','PaymentTypeController@store')->name('paymenttypes.store');

    Route::get('{id}/edit','PaymentTypeController@edit')->name('paymenttypes.edit');
    Route::delete('{id}/delete','PaymentTypeController@destroy')->name('paymenttypes.delete');
    Route::patch('{id}/update','PaymentTypeController@update')->name('paymenttypes.update');

});

Route::group(["prefix"=>'indexbcvs'],function(){
    Route::get('/','IndexBcvController@index')->name('indexbcvs');
    Route::get('register','IndexBcvController@create')->name('indexbcvs.create');
    Route::post('store','IndexBcvController@store')->name('indexbcvs.store');

    Route::get('{id}/edit','IndexBcvController@edit')->name('indexbcvs.edit');
    Route::delete('{id}/delete','IndexBcvController@destroy')->name('indexbcvs.delete');
    Route::patch('{id}/update','IndexBcvController@update')->name('indexbcvs.update');

});

Route::group(["prefix"=>'receiptvacations'],function(){
    Route::get('/','ReceiptVacationController@index')->name('receiptvacations');
    Route::get('/indexemployees','ReceiptVacationController@indexemployees')->name('receiptvacations.indexemployees');

    Route::get('register/{id}','ReceiptVacationController@create')->name('receiptvacations.create');
    Route::post('store','ReceiptVacationController@store')->name('receiptvacations.store');
    Route::get('{id}/edit','ReceiptVacationController@edit')->name('receiptvacations.edit');
    Route::delete('{id}/delete','ReceiptVacationController@destroy')->name('receiptvacations.delete');
    Route::patch('{id}/update','ReceiptVacationController@update')->name('receiptvacations.update');

});

Route::group(["prefix"=>'comisiontypes'],function(){
    Route::get('/','ComisionTypeController@index')->name('comisiontypes');
    Route::get('register','ComisionTypeController@create')->name('comisiontypes.create');
    Route::post('store','ComisionTypeController@store')->name('comisiontypes.store');

    Route::get('{id}/edit','ComisionTypeController@edit')->name('comisiontypes.edit');
    Route::delete('{id}/delete','ComisionTypeController@destroy')->name('comisiontypes.delete');
    Route::patch('{id}/update','ComisionTypeController@update')->name('comisiontypes.update');

});

Route::group(["prefix"=>'vendors'],function(){
    Route::get('/','VendorController@index')->name('vendors');
    Route::get('register','VendorController@create')->name('vendors.create');
    Route::post('store','VendorController@store')->name('vendors.store');

    Route::get('{id}/edit','VendorController@edit')->name('vendors.edit');
    Route::delete('{id}/delete','VendorController@destroy')->name('vendors.delete');
    Route::patch('{id}/update','VendorController@update')->name('vendors.update');


});

Route::group(["prefix"=>'products'],function(){
    Route::get('/','ProductController@index')->name('products');
    Route::get('register','ProductController@create')->name('products.create');
    Route::post('store','ProductController@store')->name('products.store');

    Route::get('{id}/edit','ProductController@edit')->name('products.edit');
    Route::delete('delete','ProductController@destroy')->name('products.delete');
    Route::patch('{id}/update','ProductController@update')->name('products.update');

    Route::get('listtwosubsegment/{id_subsegment}','TwoSubSegmentController@list2subsegment')->name('products.list2subsegment');
    Route::get('listtwosubsegment/{id_subsegment}','TwoSubSegmentController@list')->name('products.listtwosubsegment');
    Route::get('listthreesubsegment/{id_subsegment}','ThreeSubSegmentController@list')->name('products.listthreesubsegment');

});

Route::group(["prefix"=>'inventories'],function(){
    Route::get('/','InventoryController@index')->name('inventories');
    Route::get('selectproduct','InventoryController@selectproduct')->name('inventories.select');
    Route::post('store','InventoryController@store')->name('inventories.store');

    Route::get('{id}/edit','InventoryController@edit')->name('inventories.edit');
    Route::delete('{id}/delete','InventoryController@destroy')->name('inventories.delete');
    Route::patch('{id}/update','InventoryController@update')->name('inventories.update');

    Route::get('{id}/create','InventoryController@create')->name('inventories.create');

    Route::post('storeincreaseinventory','InventoryController@store_increase_inventory')->name('inventories.store_increase_inventory');
    Route::get('createincreaseinventory/{id_inventario}','InventoryController@create_increase_inventory')->name('inventories.create_increase_inventory');

    Route::post('storedecreaseinventory','InventoryController@store_decrease_inventory')->name('inventories.store_decrease_inventory');
    Route::get('createdecreaseinventory/{id_inventario}','InventoryController@create_decrease_inventory')->name('inventories.create_decrease_inventory');
    Route::get('movements','InventoryController@indexmovements')->name('inventories.movement');

});

Route::group(["prefix"=>'modelos'],function(){
    Route::get('/','ModeloController@index')->name('modelos');
    Route::get('register','ModeloController@create')->name('modelos.create');
    Route::post('store','ModeloController@store')->name('modelos.store');
    Route::get('{id}/edit','ModeloController@edit')->name('modelos.edit');
    Route::delete('{id}/delete','ModeloController@destroy')->name('modelos.delete');
    Route::patch('{id}/update','ModeloController@update')->name('modelos.update');
});
Route::group(["prefix"=>'colors'],function(){
    Route::get('/','ColorController@index')->name('colors');
    Route::get('register','ColorController@create')->name('colors.create');
    Route::post('store','ColorController@store')->name('colors.store');
    Route::get('{id}/edit','ColorController@edit')->name('colors.edit');
    Route::delete('{id}/delete','ColorController@destroy')->name('colors.delete');
    Route::patch('{id}/update','ColorController@update')->name('colors.update');
});
Route::group(["prefix"=>'transports'],function(){
    Route::get('/','TransportController@index')->name('transports');
    Route::get('register','TransportController@create')->name('transports.create');
    Route::post('store','TransportController@store')->name('transports.store');
    Route::get('{id}/edit','TransportController@edit')->name('transports.edit');
    Route::delete('{id}/delete','TransportController@destroy')->name('transports.delete');
    Route::patch('{id}/update','TransportController@update')->name('transports.update');
});

Route::group(["prefix"=>'historictransports'],function(){
    Route::get('/','HistoricTransportController@index')->name('historictransports');
    Route::post('store','HistoricTransportController@store')->name('historictransports.store');
    Route::get('{id}/edit','HistoricTransportController@edit')->name('historictransports.edit');
    Route::delete('{id}/delete','HistoricTransportController@destroy')->name('historictransports.delete');
    Route::patch('{id}/update','HistoricTransportController@update')->name('historictransports.update');

    Route::get('selecttransport','HistoricTransportController@selecttransport')->name('historictransports.selecttransport');
    Route::get('{idtransport}/selectemployee','HistoricTransportController@selectemployee')->name('historictransports.selectemployee');
    Route::get('{idtransport}/{idemployee}/create','HistoricTransportController@create')->name('historictransports.create');
});


Route::group(["prefix"=>'accounts'],function(){
    Route::get('menu/{coin?}/{level?}','AccountController@index')->name('accounts');
    Route::get('register','AccountController@create')->name('accounts.create');
    Route::post('store','AccountController@store')->name('accounts.store');
    Route::get('{id}/edit','AccountController@edit')->name('accounts.edit');
    Route::delete('{id}/delete','AccountController@destroy')->name('accounts.delete');
    Route::patch('{id}/update','AccountController@update')->name('accounts.update');

    Route::post('store/newlevel','AccountController@storeNewLevel')->name('accounts.storeNewLevel');


    Route::get('register/{id_account}','AccountController@createlevel')->name('accounts.createlevel');

    Route::get('movementaccount/{id_account}/{coin?}','AccountController@movements')->name('accounts.movements');

    Route::get('movementheader/{id}/{type}/{id_account}','AccountController@header_movements')->name('accounts.header_movements');

    Route::get('yearend','AccountController@year_end')->name('accounts.year_end');

    Route::get('indexpreviousexercise','AccountController@index_previous_exercise')->name('accounts.index_previous_exercise');

});

Route::group(["prefix"=>'headervouchers'],function(){
    Route::get('/','HeaderVoucherController@index')->name('headervouchers');
    Route::get('register','HeaderVoucherController@create')->name('headervouchers.create');
    Route::post('store','HeaderVoucherController@store')->name('headervouchers.store');
    Route::get('{id}/edit','HeaderVoucherController@edit')->name('headervouchers.edit');
    Route::delete('{id}/delete','HeaderVoucherController@destroy')->name('headervouchers.delete');
    Route::post('update','HeaderVoucherController@update')->name('headervouchers.update');

});

Route::group(["prefix"=>'detailvouchers'],function(){
    Route::get('/','DetailVoucherController@index')->name('detailvouchers');
    Route::get('register/{coin}/{id_header?}/{id_account?}','DetailVoucherController@create')->name('detailvouchers.create');
    Route::post('store','DetailVoucherController@store')->name('detailvouchers.store');
    Route::get('edit/{coin}/{id}/{id_account?}','DetailVoucherController@edit')->name('detailvouchers.edit');
    Route::get('delete/{id?}','DetailVoucherController@destroy')->name('detailvouchers.delete');
    Route::patch('{id}/update','DetailVoucherController@update')->name('detailvouchers.update');

    Route::get('selectaccount/{coin}/{id_header}/{id_detail?}','DetailVoucherController@selectaccount')->name('detailvouchers.selectaccount');

    Route::get('selectheadervouche','DetailVoucherController@selectheader')->name('detailvouchers.selectheadervouche');

   // Route::get('register/{coin}/{id_header}','DetailVoucherController@createselect')->name('detailvouchers.createselect');

    //Route::get('register/{coin}/{id_header}/{code_one}/{code_two}/{code_three}/{code_four}/{period}','DetailVoucherController@createselectaccount')->name('detailvouchers.createselectaccount');

    Route::get('contabilizar/{coin}/{id_header}','DetailVoucherController@contabilizar')->name('detailvouchers.contabilizar');

    Route::get('listheader/{var?}','DetailVoucherController@listheader')->name('detailvouchers.listheader');


    Route::get('validation/{coin}/{id_header?}/{id_account?}','DetailVoucherController@createvalidation')->name('detailvouchers.createvalidation');
});

Route::group(["prefix"=>'quotations'],function(){
    Route::get('/','QuotationController@index')->name('quotations');
    Route::get('register/{id_quotation}/{coin}','QuotationController@create')->name('quotations.create');
    Route::post('store','QuotationController@store')->name('quotations.store');
    Route::get('{id}/edit','QuotationController@edit')->name('quotations.edit');
    Route::delete('{id}/delete','QuotationController@destroy')->name('quotations.delete');
    Route::patch('{id}/update','QuotationController@update')->name('quotations.update');

    Route::get('registerquotation','QuotationController@createquotation')->name('quotations.createquotation');

    Route::get('registerquotation/{id_client}','QuotationController@createquotationclient')->name('quotations.createquotationclient');
    Route::get('selectclient','QuotationController@selectclient')->name('quotations.selectclient');

    Route::get('registerquotation/{id_client}/{id_vendor}','QuotationController@createquotationvendor')->name('quotations.createquotationvendor');
    Route::get('selectvendor/{id_client}','QuotationController@selectvendor')->name('quotations.selectvendor');


    Route::get('selectproduct/{id_quotation}/{coin}/{type}','QuotationController@selectproduct')->name('quotations.selectproduct');
    Route::get('registerproduct/{id_quotation}/{coin}/{id_product}','QuotationController@createproduct')->name('quotations.createproduct');


    Route::post('storeproduct','QuotationController@storeproduct')->name('quotations.storeproduct');

    Route::get('facturar/{id_quotation}/{coin}','FacturarController@createfacturar')->name('quotations.createfacturar');

    Route::post('storefactura','FacturarController@storefactura')->name('quotations.storefactura');
    Route::get('facturado/{id_quotation}/{coin}/{reverso?}','FacturarController@createfacturado')->name('quotations.createfacturado');

    Route::get('listinventory/{var?}','QuotationController@listinventory')->name('quotations.listinventory');


    Route::get('notadeentrega/{id_quotation}/{coin}','DeliveryNoteController@createdeliverynote')->name('quotations.createdeliverynote');

    Route::get('indexnotasdeentrega/','DeliveryNoteController@index')->name('quotations.indexdeliverynote');

    Route::get('quotationproduct/{id}/{coin}/edit','QuotationController@editquotationproduct')->name('quotations.productedit');
    Route::patch('productupdate/{id}/update','QuotationController@updatequotationproduct')->name('quotations.productupdate');

    Route::post('storefacturacredit','FacturarController@storefacturacredit')->name('quotations.storefacturacredit');


    Route::get('facturarafter/{id_quotation}/{coin}','FacturarController@createfacturar_after')->name('quotations.createfacturar_after');

    Route::get('refreshrate/{id_quotation}/{coin}/{rate}','QuotationController@refreshrate')->name('quotations.refreshrate');

    Route::delete('deleteproduct','QuotationController@deleteProduct')->name('quotations.deleteProduct');
    Route::delete('deletequotation','QuotationController@deleteQuotation')->name('quotations.deleteQuotation');

    Route::delete('reversarquotation','QuotationController@reversar_quotation')->name('quotations.reversarQuotation');

    Route::get('reversarquotationmultipayment/{id}/{id_header?}','QuotationController@reversar_quotation_multipayment')->name('quotations.reversar_quotation_multipayment');

    Route::delete('reversardeliverynote','DeliveryNoteController@reversar_delivery_note')->name('quotations.reversar_delivery_note');

});

Route::group(["prefix"=>'bankmovements'],function(){
    Route::get('/','BankMovementController@index')->name('bankmovements');
    Route::post('store','BankMovementController@store')->name('bankmovements.store');
    Route::get('{id}/edit','BankMovementController@edit')->name('bankmovements.edit');
    Route::get('delete/{id}','BankMovementController@destroy')->name('bankmovements.delete');
    Route::patch('{id}/update','BankMovementController@update')->name('bankmovements.update');

    Route::get('registerdeposit/{id_account}','BankMovementController@createdeposit')->name('bankmovements.createdeposit');
    Route::get('registerretirement/{id_account}','BankMovementController@createretirement')->name('bankmovements.createretirement');

    Route::get('list/{contrapartida_id?}','BankMovementController@list')->name('bankmovements.list');
    Route::get('listbeneficiario/{beneficiario_id?}','BankMovementController@listbeneficiario')->name('bankmovements.listbeneficiario');

    Route::post('storeretirement','BankMovementController@storeretirement')->name('bankmovements.storeretirement');

    Route::get('registertransfer/{id_account}','BankMovementController@createtransfer')->name('bankmovements.createtransfer');
    Route::post('storetransfer','BankMovementController@storetransfer')->name('bankmovements.storetransfer');

    Route::get('seemovements','BankMovementController@indexmovement')->name('bankmovements.indexmovement');

});

Route::group(["prefix"=>'nominas'],function(){
    Route::get('/','NominaController@index')->name('nominas');
    Route::get('register','NominaController@create')->name('nominas.create');
    Route::post('store','NominaController@store')->name('nominas.store');
    Route::get('{id}/edit','NominaController@edit')->name('nominas.edit');
    Route::delete('{id}/delete','NominaController@destroy')->name('nominas.delete');
    Route::patch('{id}/update','NominaController@update')->name('nominas.update');

    Route::get('selectemployee/{id}','NominaController@selectemployee')->name('nominas.selectemployee');

    Route::get('calculate/{id}','NominaController@calculate')->name('nominas.calculate');


});


Route::group(["prefix"=>'nominaconcepts'],function(){
    Route::get('/','NominaConceptController@index')->name('nominaconcepts');
    Route::get('register','NominaConceptController@create')->name('nominaconcepts.create');
    Route::post('store','NominaConceptController@store')->name('nominaconcepts.store');
    Route::get('{id}/edit','NominaConceptController@edit')->name('nominaconcepts.edit');
    Route::delete('{id}/delete','NominaConceptController@destroy')->name('nominaconcepts.delete');
    Route::patch('{id}/update','NominaConceptController@update')->name('nominaconcepts.update');


});

Route::group(["prefix"=>'nominacalculations'],function(){
    Route::get('index/{id_nomina}/{id_employee}','NominaCalculationController@index')->name('nominacalculations');
    Route::get('register/{id_nomina}/{id_employee}','NominaCalculationController@create')->name('nominacalculations.create');
    Route::post('store','NominaCalculationController@store')->name('nominacalculations.store');
    Route::get('{id}/edit','NominaCalculationController@edit')->name('nominacalculations.edit');
    Route::delete('{id}/delete','NominaCalculationController@destroy')->name('nominacalculations.delete');
    Route::patch('{id}/update','NominaCalculationController@update')->name('nominacalculations.update');

    Route::get('listformula/{id?}','NominaCalculationController@listformula')->name('nominacalculations.listformula');
    Route::get('listformulamensual/{id?}','NominaCalculationController@listformulamensual')->name('nominacalculations.listformulamensual');
    Route::get('listformulasemanal/{id?}','NominaCalculationController@listformulasemanal')->name('nominacalculations.listformulasemanal');
});

Route::group(["prefix"=>'invoices'],function(){
    Route::get('/','InvoiceController@index')->name('invoices');

    Route::get('movementinvoice/{id_invoice}/{coin?}','InvoiceController@movementsinvoice')->name('invoices.movement');

    Route::post('multipayment','InvoiceController@multipayment')->name('invoices.multipayment');
    Route::post('storemultipayment','InvoiceController@storemultipayment')->name('invoices.storemultipayment');

 });

 Route::group(["prefix"=>'pdf'],function(){
    Route::get('factura/{id_quotation}/{coin?}','PDF2Controller@imprimirfactura')->name('pdf');
    Route::get('deliverynote/{id_quotation}/{coin}/{iva}/{date}','PDF2Controller@deliverynote')->name('pdf.deliverynote');
    Route::get('deliverynotemediacarta/{id_quotation}/{coin}/{iva}/{date}','PDF2Controller@deliverynotemediacarta')->name('pdf.deliverynotemediacarta');
    

    Route::get('inventory','PDF2Controller@imprimirinventory')->name('pdf.inventory');

    Route::get('facturamedia/{id_quotation}/{coin?}','PDF2Controller@imprimirfactura_media')->name('pdf.media');

    Route::get('factura_maq/{id_quotation}/{coin?}','PDF2Controller@imprimirfactura_maq')->name('pdf.maq');

    Route::get('expense/{id_expense}/{coin}','PDF2Controller@imprimirExpense')->name('pdf.expense');

    Route::get('expensemedia/{id_expense}/{coin}','PDF2Controller@imprimirExpenseMedia')->name('pdf.expense_media');

    Route::get('previousexercise/{date_begin}/{date_end}','PDF2Controller@print_previousexercise')->name('pdf.previousexercise');

    Route::get('deliverynoteexpense/{id_expense}/{coin}/{iva}/{date}','PDF2Controller@deliverynote_expense')->name('pdf.deliverynote_expense');

    Route::get('order/{id_quotation}/{coin}/{iva}/{date}','PDF2Controller@order')->name('pdf.order');

});


 Route::group(["prefix"=>'tasas'],function(){
    Route::get('/','TasaController@index')->name('tasas');
    Route::get('register','TasaController@create')->name('tasas.create');
    Route::post('store', 'tasaController@store')->name('tasas.store');

    Route::get('{id}/edit','TasaController@edit')->name('tasas.edit');
    Route::delete('{id}/delete','TasaController@destroy')->name('tasas.delete');
    Route::patch('{id}/update','TasaController@update')->name('tasas.update');

});



Route::group(["prefix"=>'anticipos'],function(){
    Route::get('/','AnticipoController@index')->name('anticipos');
    Route::get('register','AnticipoController@create')->name('anticipos.create');
    Route::post('store', 'AnticipoController@store')->name('anticipos.store');

    Route::get('edit/{id}/{id_client?}/{id_provider?}','AnticipoController@edit')->name('anticipos.edit');
   Route::patch('{id}/update','AnticipoController@update')->name('anticipos.update');

    Route::get('register/{id_client}','AnticipoController@createclient')->name('anticipos.createclient');
    Route::get('selectclient/{id_anticipo?}','AnticipoController@selectclient')->name('anticipos.selectclient');

    Route::get('historic','AnticipoController@indexhistoric')->name('anticipos.historic');

    Route::get('selectanticipo/{id_client}/{coin}/{id_quotation}','AnticipoController@selectanticipo')->name('anticipos.selectanticipo');

    Route::get('changestatus/{id_anticipo}/{verify}','AnticipoController@changestatus')->name('anticipos.changestatus');

    Route::get('indexprovider','AnticipoController@index_provider')->name('anticipos.index_provider');
    Route::get('historicprovider','AnticipoController@indexhistoric_provider')->name('anticipos.historic_provider');
    Route::get('registerprovider/{id_provider?}','AnticipoController@create_provider')->name('anticipos.create_provider');
    Route::get('selectprovider/{id_anticipo?}','AnticipoController@selectprovider')->name('anticipos.selectprovider');
    Route::get('selectanticipoexpense/{id_provider}/{coin}/{id_expense}','AnticipoController@selectanticipo_provider')->name('anticipos.selectanticipo_provider');
    Route::post('storeprovider', 'AnticipoController@store_provider')->name('anticipos.store_provider');

    Route::delete('delete','AnticipoController@delete_anticipo')->name('anticipos.delete');
    Route::delete('deleteprovider','AnticipoController@delete_anticipo_provider')->name('anticipos.delete_provider');
});


Route::group(["prefix"=>'sales'],function(){
    Route::get('/','SaleController@index')->name('sales');
    /*Route::get('register','SaleController@create')->name('sales.create');
    Route::post('store', 'saleController@store')->name('sales.store');

    Route::get('{id}/edit','SaleController@edit')->name('sales.edit');
    Route::delete('{id}/delete','SaleController@destroy')->name('sales.delete');
    Route::patch('{id}/update','SaleController@update')->name('sales.update');*/

});


Route::group(["prefix"=>'expensesandpurchases'],function(){
    Route::get('/','ExpensesAndPurchaseController@index')->name('expensesandpurchases');
    Route::get('registerexpense/{id_provider?}','ExpensesAndPurchaseController@create_expense')->name('expensesandpurchases.create');
    Route::post('store', 'ExpensesAndPurchaseController@store')->name('expensesandpurchases.store');

    Route::get('{id}/edit','ExpensesAndPurchaseController@edit')->name('expensesandpurchases.edit');
    Route::delete('delete','ExpensesAndPurchaseController@destroy')->name('expensesandpurchases.delete');
    Route::delete('deletedetail','ExpensesAndPurchaseController@deleteDetail')->name('expensesandpurchases.deleteDetail');
    Route::patch('{id}/update','ExpensesAndPurchaseController@update')->name('expensesandpurchases.update');


    Route::get('selectprovider','ExpensesAndPurchaseController@selectprovider')->name('expensesandpurchases.selectprovider');

    Route::get('register/{id_expense}/{coin}/{type?}/{id_inventory?}','ExpensesAndPurchaseController@create_expense_detail')->name('expensesandpurchases.create_detail');

    Route::get('listaccount/{type_var?}','ExpensesAndPurchaseController@listaccount')->name('expensesandpurchases.listaccount');

    Route::post('storedetail', 'ExpensesAndPurchaseController@store_detail')->name('expensesandpurchases.store_detail');

    Route::get('registerpayment/{id_expense}/{coin}','ExpensesAndPurchaseController@create_payment')->name('expensesandpurchases.create_payment');

    Route::patch('storepayment','ExpensesAndPurchaseController@store_payment')->name('expensesandpurchases.store_payment');

    Route::get('indexhistorial','ExpensesAndPurchaseController@index_historial')->name('expensesandpurchases.index_historial');

    Route::post('storeexpensecredit', 'ExpensesAndPurchaseController@store_expense_credit')->name('expensesandpurchases.store_expense_credit');

    Route::get('selectinventary/{id_expense}/{coin}/{type}','ExpensesAndPurchaseController@selectinventary')->name('expensesandpurchases.selectinventary');

    Route::get('expensevoucher/{id_expense}/{coin}','ExpensesAndPurchaseController@create_expense_voucher')->name('expensesandpurchases.create_expense_voucher');

    Route::get('registerpaymentafter/{id_expense}/{coin}','ExpensesAndPurchaseController@create_payment_after')->name('expensesandpurchases.create_payment_after');

    Route::post('storeexpensepayment', 'ExpensesAndPurchaseController@store_expense_payment')->name('expensesandpurchases.store_expense_payment');

    Route::get('movementexpense/{id_expense}/{coin}','ExpensesAndPurchaseController@movements_expense')->name('expensesandpurchases.movement');

    Route::get('refreshrate/{id_expense}/{coin}/{rate}','ExpensesAndPurchaseController@refreshrate')->name('expensesandpurchases.refreshrate');

    Route::get('productedit/{id_product}/{coin}','ExpensesAndPurchaseController@editproduct')->name('expensesandpurchases.editproduct');

    Route::patch('productupdate/{id}/{coin}','ExpensesAndPurchaseController@update_product')->name('expensesandpurchases.update_product');

    Route::get('listinventory/{code}','ExpensesAndPurchaseController@listinventory')->name('expensesandpurchases.listinventory');


    Route::get('retencioniva/{id_expense}/{coin}','ExpensesAndPurchaseController@retencion_iva')->name('expensesandpurchases.retencioniva');
    Route::get('retencionislr/{id_expense}/{coin}','ExpensesAndPurchaseController@retencion_islr')->name('expensesandpurchases.retencionislr');


    Route::post('multipayment','ExpensesMultipaymentController@multipayment')->name('expensesandpurchases.multipayment');
    Route::post('storemultipayment','ExpensesMultipaymentController@storemultipayment')->name('expensesandpurchases.storemultipayment');

    Route::get('reversarcompra/{id_expense}','ExpensesAndPurchaseController@reversar_expense')->name('expensesandpurchases.reversar_expense');

    Route::get('notadeentregaexpense/{id_expense}/{coin}','ExpensesAndPurchaseController@createdeliverynote')->name('expensesandpurchases.createdeliverynote');
    Route::get('indexnotasdeentrega/','ExpensesAndPurchaseController@index_delivery_note')->name('expensesandpurchases.indexdeliverynote');

   });

Route::group(["prefix"=>'directpaymentorders'],function(){
    Route::get('/','DirectPaymentOrderController@createretirement')->name('directpaymentorders.create');
    Route::post('store','DirectPaymentOrderController@store')->name('directpaymentorders.store');

    Route::get('listbeneficiary/{type_var?}','DirectPaymentOrderController@listbeneficiary')->name('directpaymentorders.listbeneficiary');
    Route::get('listcontrapartida/{type_var?}','DirectPaymentOrderController@listcontrapartida')->name('directpaymentorders.listcontrapartida');
});


Route::group(["prefix"=>'inventarytypes'],function(){
    Route::get('/','InventaryTypeController@index')->name('inventarytypes');
    Route::get('create','InventaryTypeController@create')->name('inventarytypes.create');
    Route::post('store','InventaryTypeController@store')->name('inventarytypes.store');
    Route::get('{id}/edit','InventaryTypeController@edit')->name('inventarytypes.edit');
    Route::patch('{id}/update','InventaryTypeController@update')->name('inventarytypes.update');

});

Route::group(["prefix"=>'ratetypes'],function(){
    Route::get('/','RateTypeController@index')->name('ratetypes');
    Route::get('create','RateTypeController@create')->name('ratetypes.create');
    Route::post('store','RateTypeController@store')->name('ratetypes.store');
    Route::get('{id}/edit','RateTypeController@edit')->name('ratetypes.edit');
    Route::patch('{id}/update','RateTypeController@update')->name('ratetypes.update');

});

Route::group(["prefix"=>'companies'],function(){
    Route::get('/','CompaniesController@index')->name('companies');
    Route::get('register','CompaniesController@create')->name('companies.create');
    Route::post('store','CompaniesController@store')->name('companies.store');

    Route::get('{id}/edit','CompaniesController@edit')->name('companies.edit');
    Route::delete('{id}/delete','CompaniesController@destroy')->name('companies.delete');
    Route::patch('{id}/update','CompaniesController@update')->name('companies.update');

    Route::get('bcvlist','CompaniesController@bcvlist')->name('companies.bcvlist');
});

Route::group(["prefix"=>'nominaformulas'],function(){
    Route::get('/','NominaFormulaController@index')->name('nominaformulas');
    Route::get('register','NominaFormulaController@create')->name('nominaformulas.create');
    Route::post('store','NominaFormulaController@store')->name('nominaformulas.store');
    Route::get('{id}/edit','NominaFormulaController@edit')->name('nominaformulas.edit');
    Route::delete('{id}/delete','NominaFormulaController@destroy')->name('nominaformulas.delete');
    Route::patch('{id}/update','NominaFormulaController@update')->name('nominaformulas.update');

    Route::get('calcularlunes','NominaController@calcular_cantidad_de_lunes')->name('nominaformulas.calcular_cantidad_de_lunes');
});

Route::group(["prefix"=>'pdfnomina'],function(){
    Route::post('recibovacaciones','PdfNominaController@imprimirVacaciones')->name('pdfnomina.vacaciones');
    Route::post('reciboprestaciones','PdfNominaController@imprimirPrestaciones')->name('pdfnomina.prestaciones');
    Route::post('reciboutilidades','PdfNominaController@imprimirUtilidades')->name('pdfnomina.utilidades');
    Route::post('reciboliquidacionauto','PdfNominaController@imprimirLiquidacionAuto')->name('pdfnomina.liquidacion_auto');

    Route::get('recibovacaciones','PdfNominaController@create_recibo_vacaciones')->name('nominas.create_recibo_vacaciones');
    Route::get('reciboprestaciones','PdfNominaController@create_recibo_prestaciones')->name('nominas.create_recibo_prestaciones');
    Route::get('reciboutilidades','PdfNominaController@create_recibo_utilidades')->name('nominas.create_recibo_utilidades');
    Route::get('reciboliquidacionauto','PdfNominaController@create_recibo_liquidacion_auto')->name('nominas.create_recibo_liquidacion_auto');

    Route::get('printnominacalculation/{id_nomina}/{id_employee}','PdfNominaController@print_nomina_calculation')->name('nominas.print_nomina_calculation');

 });

 
Route::group(["prefix"=>'twosubsegments'],function(){
    Route::get('/','TwoSubSegmentController@index')->name('twosubsegments');
    Route::get('register','TwoSubSegmentController@create')->name('twosubsegments.create');
    Route::post('store', 'TwoSubSegmentController@store')->name('twosubsegments.store');

    Route::get('{id}/edit','TwoSubSegmentController@edit')->name('twosubsegments.edit');
    Route::delete('{id}/delete','TwoSubSegmentController@destroy')->name('twosubsegments.delete');
    Route::patch('{id}/update','TwoSubSegmentController@update')->name('twosubsegments.update');


    Route::get('list/{subsegment_id?}','TwoSubSegmentController@list')->name('twosubsegments.list');

});

Route::group(["prefix"=>'threesubsegments'],function(){
    Route::get('/','ThreeSubSegmentController@index')->name('threesubsegments');
    Route::get('register','ThreeSubSegmentController@create')->name('threesubsegments.create');
    Route::post('store', 'ThreeSubSegmentController@store')->name('threesubsegments.store');

    Route::get('{id}/edit','ThreeSubSegmentController@edit')->name('threesubsegments.edit');
    Route::delete('{id}/delete','ThreeSubSegmentController@destroy')->name('threesubsegments.delete');
    Route::patch('{id}/update','ThreeSubSegmentController@update')->name('threesubsegments.update');


    Route::get('list/{subsegment_id?}','ThreeSubSegmentController@list')->name('threesubsegments.list');

});



Route::group(["prefix"=>'daily_listing'],function(){
    Route::get('index','DailyListingController@index')->name('daily_listing');
    Route::post('store','DailyListingController@store')->name('daily_listing.store');
    Route::post('printjournalbook','DailyListingController@print_journalbook')->name('daily_listing.print_journalbook');
    Route::post('printdiarybookdetail','DailyListingController@print_diary_book_detail')->name('daily_listing.print_diary_book_detail');

});


Route::group(["prefix"=>'balancegenerals'],function(){
    Route::get('balancegeneral','Reports\BalanceGeneralController@index')->name('balancegenerals');
    Route::post('store','Reports\BalanceGeneralController@store')->name('balancegenerals.store');
    Route::get('balancepdf/{date_begin?}/{date_end?}/{level?}/{coin?}','Reports\BalanceGeneralController@balance_pdf')->name('balancegenerals.balance_pdf');
});
Route::group(["prefix"=>'balanceingresos'],function(){
    Route::get('balance','Reports\BalanceIngresosController@index_ingresos')->name('balanceingresos');
    Route::post('store','Reports\BalanceIngresosController@store_ingresos')->name('balanceingresos.store');
    Route::get('balancepdf/{coin?}/{date_begin?}/{date_end?}/{level?}','Reports\BalanceIngresosController@balance_ingresos_pdf')->name('balanceingresos.balance_pdf');
});

Route::group(["prefix"=>'reports'],function(){
    Route::get('accountsreceivable/{typeperson}/{id_client?}','Report2Controller@index_accounts_receivable')->name('reports.accounts_receivable');
    Route::post('storeaccounts_receivable','Report2Controller@store_accounts_receivable')->name('reports.store_accounts_receivable');
    Route::get('accounts_receivablepdf/{coin}/{date_end}/{typeinvoice}/{typeperson}/{id_client_or_vendor?}','Report2Controller@accounts_receivable_pdf')->name('reports.accounts_receivable_pdf');

    Route::get('selectclient','Report2Controller@select_client')->name('reports.select_client');
    Route::get('selectvendor','Report2Controller@select_vendor')->name('reports.select_vendor');

    Route::get('select_client_note','ReportDeliveryNoteController@select_client_note')->name('reports.select_client_note'); //dacson nota de entrega
    Route::get('select_vendor_note','ReportDeliveryNoteController@select_vendor_note')->name('reports.select_vendor_note'); // dacson nota de entrega 

    Route::get('debtstopay/{id_provider?}','Report2Controller@index_debtstopay')->name('reports.debtstopay');
    Route::post('storedebtstopay','Report2Controller@store_debtstopay')->name('reports.store_debtstopay');
    Route::get('debtstopaypdf/{coin}/{date_end}/{id_provider?}','Report2Controller@debtstopay_pdf')->name('reports.debtstopay_pdf');

    Route::get('selectprovider','Report2Controller@select_provider')->name('reports.select_provider');

    Route::get('ledger','Report2Controller@index_ledger')->name('reports.ledger');
    Route::post('storeledger','Report2Controller@store_ledger')->name('reports.store_ledger');
    Route::get('ledgerpdf/{date_begin}/{date_end}','Report2Controller@ledger_pdf')->name('reports.ledger_pdf');

    Route::get('accounts','Report2Controller@index_accounts')->name('reports.accounts');
    Route::post('storeaccounts','Report2Controller@store_accounts')->name('reports.store_accounts');
    Route::get('accountspdf/{coin?}/{level?}/{date_begin?}/{date_end?}','Report2Controller@accounts_pdf')->name('reports.accounts_pdf');

    Route::get('bankmovements','Report2Controller@index_bankmovements')->name('reports.bankmovements');
    Route::post('storebankmovements','Report2Controller@store_bankmovements')->name('reports.store_bankmovements');
    Route::get('bankmovementspdf/{type}/{coin}/{date_begin}/{date_end}/{account_bank?}','Report2Controller@bankmovements_pdf')->name('reports.bankmovements_pdf');

    Route::get('sales_books','Report2Controller@index_sales_books')->name('reports.sales_books');
    Route::post('storesales_books','Report2Controller@store_sales_books')->name('reports.store_sales_books');
    Route::get('sales_bookspdf/{coin}/{date_begin}/{date_end}','Report2Controller@sales_books_pdf')->name('reports.sales_books_pdf');

    Route::get('purchases_book','Report2Controller@index_purchases_books')->name('reports.purchases_book');
    Route::post('storepurchases_book','Report2Controller@store_purchases_books')->name('reports.store_purchases_books');
    Route::get('purchases_bookpdf/{coin}/{date_begin}/{date_end}','Report2Controller@purchases_book_pdf')->name('reports.purchases_book_pdf');

    Route::get('inventory','Report2Controller@index_inventory')->name('reports.inventory');
    Route::post('storeinventory','Report2Controller@store_inventory')->name('reports.store_inventory');
    Route::get('inventorypdf/{coin}/{date_begin}/{date_end}/{name?}','Report2Controller@inventory_pdf')->name('reports.inventory_pdf');

    Route::get('operating_margin','Report2Controller@index_operating_margin')->name('reports.operating_margin');
    Route::post('storeoperating_margin','Report2Controller@store_operating_margin')->name('reports.store_operating_margin');
    Route::get('operating_marginpdf/{coin}/{date_begin}/{date_end}','Report2Controller@operating_margin_pdf')->name('reports.operating_margin_pdf');

    Route::get('clients','Report2Controller@index_clients')->name('reports.clients');
    Route::post('storeclients','Report2Controller@store_clients')->name('reports.store_clients');
    Route::get('clientspdf/{date_begin}/{date_end}/{name?}','Report2Controller@clients_pdf')->name('reports.clients_pdf');

    Route::get('providers','Report2Controller@index_providers')->name('reports.providers');
    Route::post('storeproviders','Report2Controller@store_providers')->name('reports.store_providers');
    Route::get('providerspdf/{date_begin}/{date_end}/{name?}','Report2Controller@providers_pdf')->name('reports.providers_pdf');

    Route::get('employees','Report2Controller@index_employees')->name('reports.employees');
    Route::post('storeemployees','Report2Controller@store_employees')->name('reports.store_employees');
    Route::get('employeespdf/{date_begin}/{date_end}/{name?}','Report2Controller@employees_pdf')->name('reports.employees_pdf');

    Route::get('employees','Report2Controller@index_employees')->name('reports.employees');
    Route::post('storeemployees','Report2Controller@store_employees')->name('reports.store_employees');
    Route::get('employeespdf/{date_begin}/{date_end}/{name?}','Report2Controller@employees_pdf')->name('reports.employees_pdf');

    Route::get('sales','Report2Controller@index_sales')->name('reports.sales');
    Route::post('storesales','Report2Controller@store_sales')->name('reports.store_sales');
    Route::get('salespdf/{coin}/{date_begin}/{date_end}/{name?}','Report2Controller@sales_pdf')->name('reports.sales_pdf');

    Route::get('shopping','Reports\ShoppingController@index_shopping')->name('reports.shopping');
    Route::post('storeshopping','Reports\ShoppingController@store_shopping')->name('reports.store_shopping');
    Route::get('shoppingpdf/{coin}/{date_begin}/{date_end}/{name?}','Reports\ShoppingController@shopping_pdf')->name('reports.shopping_pdf');
    
    Route::get('accounts_receivable_note/{typepersone}/{id_client_or_vendor?}','ReportDeliveryNoteController@index_accounts_receivable_note')->name('reports.accounts_receivable_note'); // dacson (report note delivery)
    Route::post('storeaccounts_receivable_note','ReportDeliveryNoteController@store_accounts_receivable_note')->name('reports.store_accounts_receivable_note'); // dacson (report note delivery)
    Route::get('accounts_receivable_note_pdf/{coin}/{date_end}/{typeinvoice}/{typepersone}/{id_client_or_vendor?}/{fecha_frist?}','ReportDeliveryNoteController@accounts_receivable_note_pdf')->name('reports.accounts_receivable_note_pdf');

});


Route::group(["prefix"=>'payments'],function(){
    Route::get('index','PaymentController@index')->name('payments');
    Route::get('movement/{id_quotation}','PaymentController@movements')->name('payments.movement');
    Route::get('pdf/{id_payment}/{coin}','PaymentController@pdf')->name('payments.pdf');

    Route::delete('deleteall','PaymentController@deleteAllPayments')->name('payments.deleteAllPayments');
});


Route::group(["prefix"=>'payment_expenses'],function(){
    Route::get('index','PaymentExpenseController@index')->name('payment_expenses');
    Route::get('movement/{id_expense}','PaymentExpenseController@movements')->name('payment_expenses.movement');
    Route::get('pdf/{id_payment}/{coin}','PaymentExpenseController@pdf')->name('payment_expenses.pdf');

    Route::delete('deleteall','PaymentExpenseController@deleteAllPayments')->name('payment_expenses.deleteAllPayments');
});

Route::group(["prefix"=>'taxes'],function(){

    Route::get('ivapaymentindex','TaxesController@iva_paymentindex')->name('taxes.iva_paymentindex');
    Route::get('ivapayment/{month}/{year}','TaxesController@iva_payment')->name('taxes.iva_payment');
    Route::get('listaccount/{type}','TaxesController@list_account')->name('taxes.list_account');
    Route::post('payment','TaxesController@store')->name('taxes.store');

    Route::get('ivaretenidopayment','TaxesController@iva_retenido_payment')->name('taxes.iva_retenido_payment');
});

Route::group(["prefix"=>'directchargeorders'],function(){
    Route::get('/','DirectChargeOrderController@create')->name('directchargeorders.create');

    Route::post('store','DirectChargeOrderController@store')->name('directchargeorders.store');
    Route::get('listbeneficiary/{type_var?}','DirectChargeOrderController@listbeneficiary')->name('directchargeorders.listbeneficiary');
    Route::get('listcontrapartida/{type_var?}','DirectChargeOrderController@listcontrapartida')->name('directchargeorders.listcontrapartida');
});


Route::group(["prefix"=>'export'],function(){
    Route::get('expense/{id}','ExcelController@export')->name('export');
    Route::get('expenseguideaccount','ExcelController@export_guide_account')->name('export.guideaccount');
    Route::get('expenseguideinventory','ExcelController@export_guide_inventory')->name('export.guideinventory');

    Route::post('expenseimport','ExcelController@import')->name('import');

    Route::get('products','ExcelController@export_product')->name('export.product_template');
    Route::post('productsimport','ExcelController@import_product')->name('import_product');
    Route::post('productsimportprocess','ExcelController@import_product_procesar')->name('import_product_procesar');

    Route::get('clients','ExcelController@export_client')->name('export.client_template');
    Route::post('clientsimport','ExcelController@import_client')->name('import_client');

    Route::get('providers','ExcelController@export_provider')->name('export.provider_template');
    Route::post('providersimport','ExcelController@import_provider')->name('import_provider');

    Route::get('accounts','ExcelController@export_account')->name('export.account_template');
    Route::post('accountsimport','ExcelController@import_account')->name('import_account');

    Route::post('productsupdatepriceimport','ExcelController@import_product_update_price')->name('import_product_update_price');

  });

  Route::group(["prefix"=>'orders'],function(){
    Route::get('/','OrderController@index')->name('orders.index');
    Route::get('order/{id_quotation}/{coin}','OrderController@create_order')->name('orders.create_order');
    Route::get('reversarorder/{id_quotation}','OrderController@reversar_order')->name('orders.reversar_order');
  });


  Route::group(["prefix"=>'combos'],function(){
    Route::get('/','ComboController@index')->name('combos');
    Route::get('create','ComboController@create')->name('combos.create');
    Route::post('store','ComboController@store')->name('combos.store');

    Route::get('{id}/edit','ComboController@edit')->name('combos.edit');
    Route::delete('delete','ComboController@destroy')->name('combos.delete');
    Route::patch('{id}/update','ComboController@update')->name('combos.update');

    Route::get('assign/{id_combo}','ComboController@create_assign')->name('combos.create_assign');
    Route::post('storeassign','ComboController@store_assign')->name('combos.store_assign');

    Route::get('updateprice/{id_combo}/{price}/{price_buy}','ComboController@updatePrice')->name('combos.update_price');
  });

Route::group(["prefix"=>'imports'],function(){
    Route::get('/','ImportController@index')->name('imports');
    Route::get('create/{id?}','ImportController@create')->name('imports.create');
    Route::post('store','ImportController@store')->name('imports.store');
    Route::get('cargar','ImportController@cargar')->name('imports.cargar');
    Route::get('selectquotation/{id}','ImportController@selectquotation')->name('imports.selectquotation');
    Route::get('selectimport/{id}','ImportController@selectimport')->name('imports.selectimport');
    Route::get('cargarDetails/{id}/{quotation?}','ImportController@cargarDetails')->name('imports.cargarDetails');
    Route::get('calcular/{id}','ImportController@calcular')->name('imports.calcular');
    Route::patch('{id}/calcularfiltro','ImportController@calcularfiltro')->name('imports.calcularfiltro');
});

Route::group(["prefix"=>'reportspayment'],function(){
    Route::get('payment/{typeperson}/{id?}','ReportPaymentController@index_payment')->name('reportspayment.payments');
    Route::post('storepayment','ReportPaymentController@store_payment')->name('reportspayment.store_payment');
    Route::get('paymentpdf/{coin}/{date_begin}/{date_end}/{typeperson}/{id_client_or_vendor?}','ReportPaymentController@payment_pdf')->name('reportspayment.payment_pdf');

    Route::get('selectclient','ReportPaymentController@select_client')->name('reportspayment.select_client');
    Route::get('selectprovider','ReportPaymentController@select_provider')->name('reportspayment.select_provider');
    Route::get('selectvendor','ReportPaymentController@select_vendor')->name('reportspayment.select_vendor');
});


Route::group(["prefix"=>'exportexpense'],function(){
    Route::post('retencionivatxt','ExportExpenseController@ivaTxt')->name('exportexpense.ivaTxt');
    Route::post('retencionislrxml','ExportExpenseController@islrXml')->name('exportexpense.islrXml');
    Route::post('retencionivaexcel','ExportExpenseController@ivaExcel')->name('exportexpense.ivaExcel');
});

Route::group(["prefix"=>'historialquotation'],function(){
    Route::get('index/{id_user?}','Historial\HistorialQuotationController@index')->name('historial_quotation');
    Route::post('store','Historial\HistorialQuotationController@store')->name('historial_quotation.store');
    Route::get('pdf/{date_begin}/{date_end}/{id_user?}','Historial\HistorialQuotationController@pdf')->name('historial_quotation.pdf');
});

Route::group(["prefix"=>'historialselect'],function(){
    Route::get('user/{route}','Historial\HistorialSelectController@selectUser')->name('historialselect.user');
   
});

Route::group(["prefix"=>'historialexpense'],function(){
    Route::get('index/{id_user?}','Historial\HistorialExpenseController@index')->name('historial_expense');
    Route::post('store','Historial\HistorialExpenseController@store')->name('historial_expense.store');
    Route::get('pdf/{date_begin}/{date_end}/{id_user?}','Historial\HistorialExpenseController@pdf')->name('historial_expense.pdf');
});


Route::group(["prefix"=>'historialanticipo'],function(){
    Route::get('index/{id_user?}','Historial\HistorialAnticipoController@index')->name('historial_anticipo');
    Route::post('store','Historial\HistorialAnticipoController@store')->name('historial_anticipo.store');
    Route::get('pdf/{date_begin}/{date_end}/{id_user?}','Historial\HistorialAnticipoController@pdf')->name('historial_anticipo.pdf');
});

Route::group(["prefix"=>'creditnotes'],function(){
    Route::get('/','CreditNoteController@index')->name('creditnotes');
    Route::get('register/{id_creditnote}/{coin}','CreditNoteController@create')->name('creditnotes.create');
    Route::post('store','CreditNoteController@store')->name('creditnotes.store');
    Route::delete('{id}/delete','CreditNoteController@destroy')->name('creditnotes.delete');
    Route::get('historial','CreditNoteController@index_historial')->name('creditnotes.historial');
   
    Route::get('registercreditnote/{id_invoice?}/{id_client?}/{id_vendor?}','CreditNoteController@createcreditnote')->name('creditnotes.createcreditnote');
    Route::get('selectclient','CreditNoteController@selectclient')->name('creditnotes.selectclient');
    Route::get('selectvendor/{id_client}','CreditNoteController@selectvendor')->name('creditnotes.selectvendor');


    Route::get('selectproduct/{id_creditnote}/{coin}/{type}','CreditNoteController@selectproduct')->name('creditnotes.selectproduct');
    Route::get('registerproduct/{id_creditnote}/{coin}/{id_product}','CreditNoteController@createproduct')->name('creditnotes.createproduct');


    Route::post('storeproduct','CreditNoteController@storeproduct')->name('creditnotes.storeproduct');

    Route::get('facturar/{id_creditnote}/{coin}','CreditNoteDetailController@createfacturar')->name('creditnotes.createfacturar');

    Route::post('storefactura','CreditNoteDetailController@storefactura')->name('creditnotes.storefactura');
    Route::get('facturado/{id_creditnote}/{coin}/{reverso?}','CreditNoteDetailController@createfacturado')->name('creditnotes.createfacturado');

    Route::get('listinventory/{var?}','CreditNoteController@listinventory')->name('creditnotes.listinventory');


    Route::get('creditnoteproduct/{id}/{coin}/edit','CreditNoteController@editcreditnoteproduct')->name('creditnotes.productedit');
    Route::patch('productupdate/{id}/update','CreditNoteController@updatecreditnoteproduct')->name('creditnotes.productupdate');

    Route::get('refreshrate/{id_creditnote}/{coin}/{rate}','CreditNoteController@refreshrate')->name('creditnotes.refreshrate');

    Route::delete('deleteproduct','CreditNoteController@deleteProduct')->name('creditnotes.deleteProduct');
    Route::delete('deletecreditnote','CreditNoteController@deletecreditnote')->name('creditnotes.deletecreditnote');

    Route::get('selectinvoice','CreditNoteController@selectinvoice')->name('creditnotes.selectinvoice');

});

Route::group(["prefix"=>'movements'],function(){
    Route::get('creditnote/{id_creditnote}/{coin?}','CreditNoteDetailController@movements')->name('movements.creditnote');
});

Route::group(["prefix"=>'accounting_adjustments'],function(){
    Route::get('index/{coin?}','AccountingAdjustmentController@index')->name('accounting_adjustments.index');
    Route::post('store','AccountingAdjustmentController@store')->name('accounting_adjustments.store');
});
