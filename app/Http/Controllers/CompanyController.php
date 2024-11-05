<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAPIList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index()
    {
        $users = UserAPIList::all();
        return view('admin.company.companies', compact('users'));
    }

    public function addCompany()
    {
        return view('admin.company.add_company');
    }

    public function store(Request $req)
    {

        // $validate = Validator::make($req->all(), [
        //     'client_name' => 'required|string',
        //     'road_prof_key' => 'required|string',
        //     'filevine_key' => 'required|string',
        // ]);

        // if ($validate->fails()) {
        //     $errors = $validate->errors()->all();
        //     $messasge = [
        //         'message' => $errors[0], 
        //         'alert-type' => 'error'];
        //     return redirect()->back()->with($messasge);
        // }

        // $json_data = json_encode($req->all());
        UserAPIList::create([
            'filevine_API_key' => $req->filevine_API_key,
            'filevine_API_secret' => $req->filevine_API_secret,
            'roadProof_API_key' => $req->road_prof_API_key,
            'client_name' => $req->client_name,
            'created_at' => Carbon::now()
        ]);

        $messasge = [
            'message' => 'Company added successfully', 
            'alert-type' => 'success'];

        return redirect()->route('companies')->with($messasge);

    }
}
