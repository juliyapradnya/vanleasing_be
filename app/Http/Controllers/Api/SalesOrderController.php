<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\SalesOrder;
use App\PurchaseOrder;
use App\RehiringOrder;
use App\VehicleSold;
use Haruncpi\LaravelIdGenerator\IdGenerator;


class SalesOrderController extends Controller
{
    public function index(){
        //$salesorders = SalesOrder::all();

        //$rehiringorder = RehiringOrder::select('rehiring_orders.id_sales_order')->get();

        $salesorders = DB::table('sales_orders')
                      ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                      ->select('sales_orders.*','purchase_orders.vehicle_registration')
                      //->paginate(request()->per_page);
                      ->get();

        if(count($salesorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showAgreementNumber(){
        $purchaseorder = PurchaseOrder::select('purchase_orders.id_sales_order')->get();
        
        $salesorder = DB::table('sales_orders')
                    ->select('id','agreement_number')
                    ->whereNotIn('id',$purchaseorder)
                    ->whereOr()
                    ->get();

        if(count($salesorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showAgreementNumberInRehiring(){
        $rehiringorder = RehiringOrder::select('rehiring_orders.id_sales_order')->get();
        
        $salesorder = DB::table('sales_orders')
                    ->select('id','agreement_number')
                    ->whereNotIn('id',$rehiringorder)
                    ->whereOr()
                    ->get();

        if(count($salesorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showAgreementNumberInVehicleSold(){
        $vehiclesold = VehicleSold::select('vehicle_solds.id_sales_order')->get();
        
        $salesorder = DB::table('sales_orders')
                    ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                    ->select('sales_orders.id','sales_orders.agreement_number','purchase_orders.vehicle_registration','sales_orders.next_step_status_sales','purchase_orders.status_next_step')
                    ->whereRaw('status_next_step in ("Available", "Hired")')
                    ->whereRaw('next_step_status_sales in ("Innactive")')
                    ->whereNotIn('sales_orders.id',$vehiclesold)
                    ->whereOr()
                    ->get();

        if(count($salesorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showActiveSales(){
        $salesorders = DB::table('sales_orders')
                      ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                      ->select('sales_orders.id','sales_orders.agreement_number',
                      'sales_orders.cust_name','sales_orders.contract_start_date','sales_orders.id_purchase_order',
                      'sales_orders.next_step_status_sales','purchase_orders.vehicle_registration')
                      ->where('next_step_status_sales','Hired')
                      ->get();

        if(count($salesorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    // public function showVehicle(){
      //  $purchaseorder = DB::table('sales_orders')
      //                   ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
    //                     ->select('sales_orders.*','purchase_orders.vehicle_registration')
    //                     //->whereRaw('vehicle_registration = "'.$vehicle_number.'"')
    //                     ->get();

    //     if(count($purchaseorder) > 0){
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $purchaseorder
    //         ],200);
    //     }
                
    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ],400);
    // }

    public function show($id){
        $salesorders = DB::table('sales_orders')
                      ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                      ->select('sales_orders.*','purchase_orders.vehicle_registration')
                      ->whereRaw('sales_orders.id = '.$id)
                      ->get();

        if(!is_null($salesorders)){
            return response([
                'message' => 'Retrieve Sales Order Success',
                'data' => $salesorders
            ],200);
        }

        return response([
            'message' => 'Sales Order Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_purchase_order'         => 'required',
            'type'                      => 'required|in:Contract Hire (Unregulated),Hire (Unregulated)',
            'agreement_no'              => 'nullable',
            'agreement_number'          => 'required|unique:sales_orders',
            'cust_name'                 => 'required',
            'contract_start_date'       => 'required|date_format:Y-m-d',
            'annual_mileage'            => 'required',
            'term_months'               => 'required',
            'initial_rental'            => 'required',
            'documentation_fees'        => 'required',
            'monthly_rental'            => 'required',
            'other_income'              => 'required',
            'margin_term'               => 'nullable',
            'total_income'              => 'nullable',
            'next_step_status_sales'    => 'nullable',
            'first_payment'             => 'nullable',
            'total_monthly_rental'      => 'nullable',
            'penalty_early_settlement'  => 'nullable',
            'settlement'                => 'nullable',
            'annum_payment'             => 'nullable',
            'sales_final_payment'       => 'nullable',
            'total_cost'                => 'nullable',
            'contract_margin'           => 'nullable',
            'rental_income'             => 'nullable',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $checkPurchaseOrderExist = SalesOrder::whereRaw('id_purchase_order = "'.$request->id_purchase_order.'" and next_step_status_sales in ("Hired", "Sold")')->get();
            if(count($checkPurchaseOrderExist) > 0){
            return response (['message' => 'Sales order cannot be processed because the car is not available'],400);
        }

        $salesorder = SalesOrder::create($storeData);
        
        $purchaseorder = PurchaseOrder::find($salesorder->id_purchase_order);

        //$salesorder->basic_list_price       = round($salesorder->basic_list_price,2);
        $salesorder->annual_mileage           = round($salesorder->annual_mileage,2);
        //$salesorder->initial_rental         = round($salesorder->initial_rental,2);
        //$salesorder->documentation_fees     = round($salesorder->documentation_fees,2);
        //$salesorder->monthly_rental         = round($salesorder->monthly_rental,2);
        //$salesorder->other_income           = round($salesorder->other_income,2);
        
        $salesorder->next_step_status_sales = 'Hired';

        //fo001
        $salesorder->margin_term = $salesorder->term_months;
        $salesorder->save();
        

        $amount_oi = SalesOrder::join('other_incomes', 'other_incomes.id_purchase_order','=','sales_orders.id_purchase_order')
        ->whereRaw('sales_orders.id_purchase_order = '.$salesorder->id_purchase_order)
        ->value('amount_oi');

        //fo006
        $salesorder->first_payment = round($salesorder->initial_rental + $salesorder->documentation_fees + $salesorder->other_income,2);
        $salesorder->save();

        //fo002
        if($salesorder->next_step_status_sales == 'Hired') {
            if($amount_oi == null){
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } else {
            if($amount_oi == null){
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } 

        
        $salesorder->total_monthly_rental = $purchaseorder->regular_monthly_payment * 11; 
        $salesorder->save();

        //fo006 annum_payment
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $salesorder->annum_payment = 0;
            $salesorder->save();
        } else {
            $salesorder->annum_payment = round($purchaseorder->monthly_payment * $salesorder->term_months ,2);
            $salesorder->save();
        } 

        //sales final payment
        $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
        $salesorder->sales_final_payment = round($purchaseorder->financing_amount * (1 + ($hp_interest_persen)) - $salesorder->total_monthly_rental,2);
        $salesorder->save();

        //fo007 settlement
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $salesorder->settlement = round($purchaseorder->price_otr,2);
            $salesorder->save();
        } else if ($purchaseorder->purchase_method != 'Cash' && $purchaseorder->purchase_method != 'Rent/Return'){
            $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
            $salesorder->settlement = round(($purchaseorder->financing_amount * (($salesorder->term_month / 12) + $hp_interest_persen) - $salesorder->annum_payment),2);
            $salesorder->save();
        } else {
            $salesorder->settlement = 0;
            $salesorder->save();
        }

        //fo008 penalty_early_settlement
        if($purchaseorder->purchase_method == 'Hire Purchase') {
            $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
            $salesorder->penalty_early_settlement = round(($salesorder->sales_final_payment * $hp_interest_persen)/11,2);
            $salesorder->save();
        } else {
            $salesorder->penalty_early_settlement = 0;
            $salesorder->save();
        }


        //fo0011 total_cost
        if($purchaseorder->purchase_method != 'Cash'){
            $salesorder->total_cost = round($purchaseorder->sum_docdepoth + $purchaseorder->final_fees +  ($purchaseorder->vehicle_tracking * 11) + $salesorder->total_monthly_rental + $salesorder->sales_final_payment + $salesorder->penalty_early_settlement,2);
            $salesorder->save();
        } else if($purchaseorder->purchase_method == 'Cash'){
            $salesorder->total_cost = round($purchaseorder->price_otr,2);
            $salesorder->save();
        }
        
        //fo0013 contract_margin
        $salesorder->contract_margin = round(($salesorder->total_income) - $purchaseorder->total_cost,2);
        $salesorder->save();

        // //rental income
        // $salesorder->rental_income = round($salesorder->monthly_rental * ($salesorder->margin_term + 1),2);
        // $salesorder->save();

        //rental income
        if($salesorder->next_step_status_sales == 'Hired') {
            if($amount_oi == null){
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } else {
            if($amount_oi == null){
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } 
        
        $salesorder->agreement_no = IdGenerator::generate(['table' => 'sales_orders','field'=>'agreement_no', 'length' => 7, 'prefix' =>'SO-']);
        //output: P00001
        $salesorder->save();

        $purchaseorder->status_next_step = 'Hired';
        $purchaseorder->save();

        return response([             
            'message' => 'Add Sales Order Success',
            'data' => $salesorder,
        ],200);
    }

    public function destroy($id){
        $salesorder = SalesOrder::find($id);     
        if(is_null($salesorder)){
            return response([
                'message' => 'Sales Order Not Found',
                'data' => null
            ],404);
        }

       $update = PurchaseOrder::where('id',$salesorder->id_purchase_order)
                      ->update(['status_next_step' => 'Available']);

        
        // return response([
        //     'message' => 'Delete Sales Order Success',
        //     'data' => $update,
        // ],200);

        if($salesorder->delete()){
            return response([
                'message' => 'Delete Sales Order Success',
                'data' => $salesorder,
            ],200);
        }
        
        return response([
            'message' => 'Delete Sales Order Failed',
            'data' => null,
        ],400);
    }

    public function update(Request $request, $id){
        $salesorder = SalesOrder::find($id);
        $oldSalesOrder = SalesOrder::find($id);
        if(is_null($salesorder)){
            return response([
                'message' => 'Sales Order Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_purchase_order'     => 'required',
            'type'                  => 'required|in:Contract Hire (Unregulated),Hire (Unregulated)',
            'agreement_no'          => 'nullable',
            'agreement_number'      => ['required', Rule::unique('sales_orders')->ignore($salesorder)],
            'cust_name'             => 'required',
            'contract_start_date'   => 'required|date_format:Y-m-d',
            'annual_mileage'        => 'required',
            'term_months'           => 'required',
            'initial_rental'        => 'required',
            'documentation_fees'    => 'required',
            'monthly_rental'        => 'required',
            'other_income'          => 'required',
            'margin_term'           => 'nullable',
            'total_income'          => 'nullable',
            'next_step_status_sales'  => 'nullable',
            'first_payment'           => 'nullable',
            'total_monthly_rental'         => 'nullable',
            'penalty_early_settlement' => 'nullable',
            'settlement'               => 'nullable',
            'annum_payment'            => 'nullable',
            'sales_final_payment'       => 'nullable',
            'total_cost'               => 'nullable',
            'contract_margin'          => 'nullable',
            'rental_income'          => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $checkPurchaseOrderExist = SalesOrder::whereRaw('id_purchase_order = "'.$request->id_purchase_order.'" and next_step_status_sales in ("Sold")')->get();
            if(count($checkPurchaseOrderExist) > 0){
            return response (['message' => 'Sales order cannot be processed because the car is not available'],400);
        }

        $salesorder->id_purchase_order     = $updateData['id_purchase_order'];
        $salesorder->type                  = $updateData['type'];
        //$salesorder->agreement_no        = $updateData['agreement_no'];
        $salesorder->agreement_number      = $updateData['agreement_number'];
        $salesorder->cust_name             = $updateData['cust_name'];
        //$salesorder->sales_person          = $updateData['sales_person'];
        $salesorder->contract_start_date   = $updateData['contract_start_date'];
        //$salesorder->vehicle_manufacturer  = $updateData['vehicle_manufacturer'];
        //$salesorder->vehicle_model         = $updateData['vehicle_model'];
        //$salesorder->vehicle_variant       = $updateData['vehicle_variant'];
        //$salesorder->basic_list_price      = $updateData['basic_list_price'];
        $salesorder->annual_mileage        = $updateData['annual_mileage'];
        $salesorder->term_months           = $updateData['term_months'];
        $salesorder->initial_rental        = $updateData['initial_rental'];
        $salesorder->documentation_fees    = $updateData['documentation_fees'];
        $salesorder->monthly_rental        = $updateData['monthly_rental'];
        $salesorder->other_income          = $updateData['other_income'];
        //$salesorder->next_step_status_sales  = $updateData['next_step_status_sales'];
        
        $purchaseorder = PurchaseOrder::find($salesorder->id_purchase_order);

        //$salesorder->basic_list_price = round($salesorder->basic_list_price,2);
        $salesorder->annual_mileage = round($salesorder->annual_mileage,2);
        //$salesorder->initial_rental = round($salesorder->initial_rental,2);
        //$salesorder->documentation_fees = round($salesorder->documentation_fees,2);
        //$salesorder->monthly_rental = round($salesorder->monthly_rental,2);
        //$salesorder->other_income = round($salesorder->other_income,2);

        //update status next step
        $purchaseorder->status_next_step = 'Hired';
        $purchaseorder->save();
        

        //fo001
        if($salesorder->term_months != null) {
            $salesorder->margin_term = $salesorder->term_months;
            $salesorder->save();
        }

        $amount_oi = SalesOrder::join('other_incomes', 'other_incomes.id_purchase_order','=','sales_orders.id_purchase_order')
        ->whereRaw('sales_orders.id_purchase_order = '.$salesorder->id_purchase_order)
        ->value('amount_oi');

        //fo006
        $salesorder->first_payment = round($salesorder->initial_rental + $salesorder->documentation_fees + $salesorder->other_income,2);
        $salesorder->save();

        //fo002
        if($salesorder->next_step_status_sales == 'Hired') {
            if($amount_oi == null){
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } else {
            if($amount_oi == null){
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->total_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        }
        
        $salesorder->total_monthly_rental = round($purchaseorder->regular_monthly_payment * 11); 
        $salesorder->save();

        //fo006 annum_payment
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $salesorder->annum_payment = 0;
            $salesorder->save();
        } else {
            $salesorder->annum_payment = round($purchaseorder->monthly_payment * $salesorder->term_months ,2);
            $salesorder->save();
        } 

        //sales final payment
        $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
        $salesorder->sales_final_payment = round($purchaseorder->financing_amount * (1 + ($hp_interest_persen)) - $salesorder->total_monthly_rental,2);
        $salesorder->save();

        //fo007 settlement
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $salesorder->settlement = round($purchaseorder->price_otr,2);
            $salesorder->save();
        } else if ($purchaseorder->purchase_method != 'Cash' && $purchaseorder->purchase_method != 'Rent/Return'){
            $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
            $salesorder->settlement = round(($purchaseorder->financing_amount * (($salesorder->term_month / 12) + $hp_interest_persen)) - $salesorder->annum_payment,2);
            $salesorder->save();
        } else {
            $salesorder->settlement = 0;
            $salesorder->save();
        }

        //fo008 penalty_early_settlement
        if($purchaseorder->purchase_method == 'Hire Purchase') {
            $hp_interest_persen = $purchaseorder->hp_interest_per_annum / 100;
            $salesorder->penalty_early_settlement = round(($salesorder->sales_final_payment * $hp_interest_persen)/11,2);
            $salesorder->save();
        } else {
            $salesorder->penalty_early_settlement = 0;
            $salesorder->save();
        }

        
        //fo0011 total_cost
        if($purchaseorder->purchase_method != 'Cash'){
            $salesorder->total_cost = round($purchaseorder->sum_docdepoth + $purchaseorder->final_fees +  ($purchaseorder->vehicle_tracking * 11) + $salesorder->total_monthly_rental + $salesorder->sales_final_payment + $salesorder->penalty_early_settlement,2);
            $salesorder->save();
        } else if($purchaseorder->purchase_method == 'Cash'){
            $salesorder->total_cost = round($purchaseorder->price_otr,2);
            $salesorder->save();
        }

        //fo0012 contract_margin
        $salesorder->contract_margin = round(($salesorder->total_income) - $purchaseorder->total_cost,2);
        $salesorder->save();

        // //rental income
        // $salesorder->rental_income = round($salesorder->monthly_rental * ($salesorder->margin_term+1),2);
        // $salesorder->save();

        //rental income
        if($salesorder->next_step_status_sales == 'Hired') {
            if($amount_oi == null){
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } else {
            if($amount_oi == null){
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + 0),2);
                $salesorder->save();
            } else {
                $salesorder->rental_income = round($salesorder->first_payment + ($salesorder->monthly_rental * ($salesorder->margin_term) + $amount_oi),2);
                $salesorder->save();
            }
        } 
        
        $salesorder->agreement_no = IdGenerator::generate(['table' => 'sales_orders','field'=>'agreement_no', 'length' => 7, 'prefix' =>'SO-']);
        //output: P00001
        $salesorder->save();

        if($salesorder->save()){
            $update = PurchaseOrder::where('id',$oldSalesOrder->id_purchase_order)
                      ->update(['status_next_step' => 'Available']);

            return response([
                'message' => 'Update Sales Order Success',
                'data' => $salesorder,
            ],200);
        }

        return response([
            'message' => 'Update Sales Order Failed',
            'data' => null
        ],400);
    }
    
    public function test(){

        $amount = DB::table('other_costs')
                      ->join('purchase_orders','purchase_orders.id','=','other_costs.id_purchase_order')
                      ->whereRaw('other_costs.id_purchase_order = '.$salesorder->id_purchase_order)
                      ->sum('other_costs.amount_oc');

        if($amount != null){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $amount
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }
}
