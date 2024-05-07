<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Currency;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Closure;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memberships = Membership::with('currency')->paginate(15);

        return view('admin.dashboard.memberships.index', ['memberships' => $memberships]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencyCodes = Currency::all();

        return view('admin.dashboard.memberships.create', ['currencyCodes' => $currencyCodes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|unique:memberships|max:100',
            'status' => 'required|boolean',
            'allowed_ads' => 'min:0',
            'allowed_pictures' => 'min:0',
            'doorstep_delivery' => 'required|boolean',
            'currency_code' => 'required|exists:currencies,code',
            'amount' => 'required|decimal:2',
            'description' => 'required|max:500',
        ]);

        $membership = new Membership;      
        $membership->name = $request->name;
        $membership->active = $request->status;
        $membership->allowed_ads = $request->allowed_ads;
        $membership->allowed_pictures = $request->allowed_pictures;
        $membership->doorstep_delivery = $request->doorstep_delivery;
        $membership->currency_code = $request->currency_code;
        $membership->amount = $request->amount;
        $membership->icon = $request->icon;
        $membership->description = $request->description;
        $membership->save();
        
        return redirect()->route("memberships.index");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $currencyCodes = Currency::all();

        $membership = Membership::with("currency")->whereId($id)->first();

        return view('admin.dashboard.memberships.edit', ['membership' => $membership, 'currencyCodes' => $currencyCodes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(isset($request->statusUpdate)){

            try {
                $membership = Membership::find($id);      
                $membership->active = ( $request->status == 'true' ) ? 1 : 0;
                $membership->save();
            } catch (\Throwable $th) {
                        
                return response()->json([
                        'success' => false,
                        'message' => 'Status do not updated.',
                 ], 500);
            }

           return response()->json([
                'success' => true,
                'message' => $membership->name.' status updated.',
                'data' => [
                    'status' => $membership->active,
                ]
            ], 200);
        }else if(isset($request->singleUpdate)){

            $validated = $request->validate([
                'name' => ['required', 'max:100', function (string $attribute, mixed $value, Closure $fail) {
                    GLOBAL $id;
                    if (Membership::whereNotIn('id', [$id])->where('name', $value)->exists()) {
                        $fail("The {$attribute} has already been taken.");
                    }
                },],
                'status' => 'required|boolean',
                'allowed_ads' => 'min:0',
                'allowed_pictures' => 'min:0',
                'doorstep_delivery' => 'required|boolean',
                'currency_code' => 'required|exists:currencies,code',
                'amount' => 'required|decimal:2',
                'description' => 'required|max:500',
            ]);

            $membership = Membership::find($id);
            $membership->name = $request->name;
            $membership->active = $request->status;
            $membership->allowed_ads = $request->allowed_ads;
            $membership->allowed_pictures = $request->allowed_pictures;
            $membership->doorstep_delivery = $request->doorstep_delivery;
            $membership->currency_code = $request->currency_code;
            $membership->amount = $request->amount;
            $membership->icon = $request->icon;
            $membership->description = $request->description;
            $membership->save();
            
            return redirect()->route("memberships.index");
        }else{     
            return response()->json([
                'success' => false,
                'message' => 'Bad request.',
            ], 400);
        }

    }

    public function massUpdate(Request $request, string $function){

        if(isset($request->memberships)){

            try {

                switch ($function) {
                    case 'activate':
                        foreach ($request->memberships as $key => $value) {
                                
                            $membership = Membership::find($value);      
                            $membership->active = 1;
                            $membership->save();
                        }
                        break;
                    
                    case 'deactivate':
                        foreach ($request->memberships as $key => $value) {
                                
                            $membership = Membership::find($value);      
                            $membership->active = 0;
                            $membership->save();
                        }
                        break;
                    
                    case 'delete':
                        Membership::destroy($request->memberships);
                        break;
                    
                    default:
                        return response()->json([
                            'success' => false,
                            'message' => 'Not found.',
                        ], 404);
                        break;
                }

            } catch (\Throwable $th) {
                        
                return response()->json([
                    'success' => false,
                    'message' => 'Memberships do not updated.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Memberships updated.',
                'data' => [
                    'status' => 1,
                ]
            ], 200);
                
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Bad request.',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Membership::destroy($id);
        } catch (\Throwable $th) {
                
            return response()->json([
                'success' => false,
                'message' => 'Memberships do not deleted.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Memberships deleted.',
            'data' => [
                'status' => 1,
            ]
        ], 200);
    }

    public function showUserMemberships(){

        $users = User::with('membership')->paginate(15);

        $memberships = Membership::all();

        return view('admin.dashboard.memberships.user-memberships', ['users' => $users, 'memberships'=>$memberships]);
    }

    public function updateUserMemberships(Request $request, string $id){
        if(isset($request->membership)){
            try {
                $user = User::find($id);      
                $membership = Membership::find($request->membership);
                $user->membership()->associate($membership);
                $user->save();
            } catch (\Throwable $th) {
                        
                return response()->json([
                        'success' => false,
                        'message' => 'User membership do not updated.',
                 ], 500);
            }

           return response()->json([
                'success' => true,
                'message' => $user->name.' membership updated.',
                'data' => [
                    'membership' => $user->membership->id,
                ]
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Requested membership id of the user did not found.',
            ], 500);
        }
    }
}
