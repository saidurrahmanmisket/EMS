<?php

namespace App\Http\Controllers;

use App\Models\ExpenseEmployee;
use App\Models\ExpenseEmployeeAdvance;
use App\Models\ExpenseProduct;
use App\Models\ExpenseRental;
use App\Models\ExpenseSector;
use App\Models\ExpenseUtility;
use App\Models\PaymentEmployee;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($expenseSector, $expenseId)
    {
        try {
            $expenseSector = ExpenseSector::select('name','slug')
                ->where('slug', $expenseSector)
                ->first();

            if (empty($expenseSector)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense sector not found!',
                ], 200);
            }

            if ($expenseSector->slug == 'rental') {     // Rental
                $expenseData = ExpenseRental::getRentalExpenseDataForPayment($expenseId);
            } else if ($expenseSector->slug == 'employee') {        // Employee
                $expenseData = ExpenseEmployee::getEmployeeExpenseDataForPayment($expenseId);
            }else if ($expenseSector->slug == 'utility') {
                $expenseData = ExpenseUtility::getutilityExpenseDataForPayment($expenseId);
            }else if ($expenseSector->slug == 'product') {         // Product
                $expenseData = ExpenseProduct::getProductExpenseDataForPayment($expenseId);

            }

            return response()->json([
                'suceess' => true,
                'message' => ' Expense Payment Information found',
                'data' => $expenseData,
            ], 200);
        } catch (\Exception $e) {
            Log::debug('Unable to create payment. Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::debug($request->all());

        try {

            $validatedData = $request->validate([
                'expense_id' => 'required',
                'payment_sector' => 'required|in:product,employee,rental,utility',//add other ...
                'payment_method_id' => 'required',
                'payment_client_giver_id' => 'required',
                'slip_image' => 'required|image|max:3072',
            ]);

            $payment = new Payment();
            $payment->expense_id = $request->expense_id;
            $payment->payment_method_id = $request->payment_method_id;
            $payment->bank_account_id = $request->bank_account_id;
            $payment->transaction_type_id = $request->transaction_type_id;
            $payment->slip_no = $request->slip_no;
            $payment->mobile_banking_operator_id = $request->mobile_banking_operator_id;
            $payment->transaction_number = $request->transaction_number;
            $payment->date = $request->date;
            $payment->payment_receiver_id = $request->payment_receiver_id;
            $payment->payment_client_giver_id = $request->payment_client_giver_id;
            $payment->payment_approved_by_employee_id = $request->payment_approved_by_employee_id;
            $payment->merchant_receiver_banking_name_id = $request->merchant_receiver_banking_name_id;
            $payment->mob_operator_payment_name_id = $request->mob_operator_payment_name_id;
            $payment->merchant_receiver_banking_number_id = $request->merchant_receiver_banking_number_id;
            $payment->merchant_giver_banking_number_id = $request->merchant_giver_banking_number_id;
            $payment->merchant_giver_banking_name_id = $request->merchant_giver_banking_name_id;
            $payment->receiver_type = $request->receiver_type;
            $payment->giver_type = $request->giver_type;
            $payment->remarks = $request->remarks;
            // work with image
            if ($request->hasFile('slip_image')) {
                $image = $request->file('slip_image');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/payment/slip_image';
                $image->move(storage_path($imagePath), $imageName);
                $payment->slip_image = 'storage/' . $imagePath . '/' . $imageName; // save image path to database
            }

            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment Save Success',
                'data' => $payment,
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Payment',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json('success');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $paymentMethodId = $request->input('paymentMethodId');

            $dateStartFrom = $request->input('dateStartFrom');
            $dateEndTo = $request->input('dateEndTo');

            $payment = Payment::with('paymentMethod:id,payment_method')
                ->select('id','payment_sector', 'payment_method_id', 'date', 'remarks');

            if ($paymentMethodId){
                $payment->where('payment_method_id', $paymentMethodId);
            }
            if ($dateStartFrom && $dateEndTo) {
                $payment->whereBetween('date', [$dateStartFrom, $dateEndTo]);
            }
            if ($dateStartFrom) {
                $payment->where('date', '>=', $dateStartFrom);
            }
            if ($dateEndTo) {
                $payment->where('date', '<=', $dateEndTo);
            }
            $payment = $payment->paginate();

            return response()->json([
                'success' => true,
                'message' => 'Payment face Success',
                'data' => $payment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
