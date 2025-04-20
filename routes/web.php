<?php

use App\Livewire\Frontend\Analyzing;
use App\Livewire\Frontend\Questions;
use App\Livewire\Frontend\ResultPrev;
use App\Livewire\Frontend\Signup;
use App\Livewire\Frontend\Start;
use App\Livewire\Frontend\StartNow;
use Illuminate\Support\Facades\Route;


// Front routes
Route::get("/", Start::class)->name("start");
Route::get("/startnow", StartNow::class)->name("startnow");
Route::get("/question1", Questions::class)->name("question");
Route::get("/analyze", Analyzing::class)->name("analyze");
Route::get("/result-prev", ResultPrev::class)->name("resprev");
Route::get("/sign-up", Signup::class)->name("signup");


// Dashboard routes
Route::group(["middleware" => [
    'auth:sanctum',
    config("jetstream.auth_session"),
    "verified"
]], function () {
    Route::get("/dashboard", \App\Livewire\Backend\Dashboard::class)->name("dashboard");
});

//Admin dashboard
Route::group(["middleware" => [
    'auth:sanctum',
    config("jetstream.auth_session"),
    "verified",
    "admin",
]], function () {
    Route::get("/dashboard/addbook", function () {
        return "Add book route";
    });

    Route::get("/dashboard/categories", \App\Livewire\Backend\Category::class)->name("categories");
});
