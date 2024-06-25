<?php

namespace App\Http\Controllers;

use App\Models\BankAccounts;
use Illuminate\Http\Request;
use Mockery\Exception;

class BankAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->per_page;
            $page = $request->page;
            $bank_id = $request->bank_id;


            $query = BankAccounts::select([
                    'id',
                    'bank_id',
                    'account_number',
                    'holder_name'
                ])->orderBy('id');

            //query with bank_id
            if ($bank_id ){
                $query = $query->where('bank_id', $bank_id);
            }

            //query with more data with paginate for showing to table
            if($perPage || $page ){
                if ($perPage < 0 || $page < 0 ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Page Can\'t less then Zero',
                    ], 404);
                }
                $query = $query->with(['bank:id,name,company_id', 'bank.company:id,name'])
                    ->select([
                    'id',
                    'bank_id',
                    'account_number',
                    'holder_name',
                    'holder_address',
                    'holder_email',
                    'holder_profession',
                    'holder_image',
                ])->paginate($perPage);
            }
            else{
                $query = $query->get();
            }
            //check empty
            if ($query->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank accounts not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bank accounts list.',
                'data' => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'bank_id' => 'required',
                'account_number' => 'required',
                'holder_name' => 'required',
                'holder_address' => 'required',
                'holder_email' => 'required|email',
                'holder_profession' => 'required',
//                'holder_image' => 'nullable|image|max:3072',
            ]);

            // Create a new BankAccount instance with the validated data
            $bankAccount = new BankAccounts();
            $bankAccount->bank_id = $validatedData['bank_id'];
            $bankAccount->account_number = $validatedData['account_number'];
            $bankAccount->holder_name = $validatedData['holder_name'];
            $bankAccount->holder_address = $validatedData['holder_address'];
            $bankAccount->holder_email = $validatedData['holder_email'];
            $bankAccount->holder_profession = $validatedData['holder_profession'];

            // Upload and store the holder image
            if ($request->hasFile('holder_image')) {
                $image = $request->file('holder_image');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/bank_accounts_holder';
                $image->move(storage_path($imagePath), $imageName);
                $bankAccount->holder_image = 'storage/' . $imagePath . '/' . $imageName;

            }

            // Save the bank account details to the database
            $bankAccount->save();

            return response()->json([
                'success' => true,
                'message' => 'Bank account created successfully',
                'data' => $bankAccount
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\BankAccounts $bankAccounts
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccounts $bankAccounts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\BankAccounts $bankAccounts
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $acccounts = BankAccounts::with(['bank:id,name,company_id', 'bank.company:id,name'])
            ->where('id', $id)->first();

            if (empty($acccounts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank account not found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Bank account found',
                'data' => $acccounts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BankAccounts $bankAccounts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'bank_id' => 'required',
                'account_number' => 'required',
                'holder_name' => 'required',
                'holder_address' => 'required',
                'holder_email' => 'required|email',
                'holder_profession' => 'required',
                'holder_image' => 'image|max:3072',
            ]);
            // Find the bank account record by its ID
            $id = $request->id;
            $bankAccount = BankAccounts::findOrFail($id);

            if (!$bankAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank account not found',
                ], 404);
            }
            // Update the bank account instance with the validated data
            $bankAccount->bank_id = $validatedData['bank_id'];
            $bankAccount->account_number = $validatedData['account_number'];
            $bankAccount->holder_name = $validatedData['holder_name'];
            $bankAccount->holder_address = $validatedData['holder_address'];
            $bankAccount->holder_email = $validatedData['holder_email'];
            $bankAccount->holder_profession = $validatedData['holder_profession'];

            // Check if a new holder image is provided
            if ($request->hasFile('holder_image')) {
                $image = $request->file('holder_image');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/bank_accounts_holder';
                $image->move(storage_path($imagePath), $imageName);
                $bankAccount->holder_image = 'storage/' . $imagePath . '/' . $imageName;
            }

            // Save the updated bank account details to the database
            $bankAccount->save();

            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully',
                'data' => $bankAccount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BankAccounts $bankAccounts
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            // Find the bank account record by its ID
            $bankAccount = BankAccounts::find($id);

            // Check if the bank account record exists
            if (!$bankAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank account not found',
                ], 404);
            }

            // Delete the bank account record
            $bankAccount->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank account deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
