<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\Translation\Catalogue\AbstractOperation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/images/{filename}', function($filename, Request $request){
    $disk = Storage::disk('s3');
    $path = 'produse/' . $filename;

    if(!$disk->exists($path)){
        abort(404, 'Imaginea nu o fost gasita!');
    }

    // link temporar
    $url = $disk->temporaryUrl($path, now()->addMinutes(20));

    // redirectam browser spre img din r2 (link privat semnat)
    return redirect()->to($url);
});



//test
Route::get('/redis-ping', function () {
    return Redis::connection()->ping(); // trebuie să răspundă cu 'PONG'
});