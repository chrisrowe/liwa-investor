<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\SaveInvestorRequest;
use Inertia\Inertia;
use Hash;
use Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvestorWelcomeEmail;

class InvestorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Roles/Investors/Index', [
            'investors' => User::whereHas('roles',
                function($q){
                    $q->where('name', "investor");
                })->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Roles/Investors/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\SaveInvestorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveInvestorRequest $request)
    {
        $requestData = $request->all();
        $sendWelcomeEmail = !empty($requestData['send_welcome_email']);

        $password = Str::random();
        $requestData['password'] = Hash::make($password, ['rounds' => 12]);
        
        $user = User::create($requestData);
        $user->assignRole('investor');
        if ($sendWelcomeEmail) {
            Mail::to($user)->send(new InvestorWelcomeEmail($user));
        }

        return \Redirect::route('investors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Roles/Investors/CreateEdit', [
            'investor' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(SaveInvestorRequest $request, User $user)
    {
        $requestData = $request->all();
        $user->update($requestData);

        return \Redirect::route('investors.edit', [$user->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
    }
}
