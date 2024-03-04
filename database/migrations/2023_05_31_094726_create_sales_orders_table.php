<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('id_purchase_order');
            $table->string('type');
            $table->string('agreement_no');
            $table->string('agreement_number');
            $table->string('cust_name');
            $table->date('contract_start_date');
            $table->double('annual_mileage');
            $table->integer('term_months');
            $table->double('initial_rental');
            $table->double('documentation_fees');
            $table->double('monthly_rental');
            $table->double('other_income');
            $table->integer('margin_term');
            $table->double('total_income');
            $table->double('next_step_status_sales');
            $table->double('first_payment');
            $table->double('total_monthly_rental');
            $table->double('penalty_early_settlement');
            $table->double('settlement');
            $table->double('annum_payment');
            $table->double('sales_final_payment');
            $table->double('total_cost');
            $table->double('contract_margin');
            $table->double('rental_income');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_orders');
    }
}
