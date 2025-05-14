<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\SocialController;


Route::get('register', [UsersController::class, 'register'])->name('register');
Route::post('register', [UsersController::class, 'doRegister'])->name('do_register');
Route::get('login', [UsersController::class, 'login'])->name('login');
Route::post('login', [UsersController::class, 'doLogin'])->name('do_login');
Route::get('logout', [UsersController::class, 'doLogout'])->name('do_logout');
Route::get('users', [UsersController::class, 'list'])->name('users');
Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
Route::post('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
Route::get('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');
Route::post('user/{user}/add-credit', [UsersController::class, 'addCredit'])->name('user.addCredit');

Route::get('verify', [UsersController::class, 'verify'])->name('verify');

//password reset

Route::get('/forgot-password', [UsersController::class, 'forgotPasswordForm'])->name('forgot_password');
Route::post('/forgot-password', [UsersController::class, 'sendTemporaryPassword'])->name('forgot_password.submit');


Route::get('/auth/redirect/{provider}', [SocialController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback/{provider}', [SocialController::class, 'callback'])->name('auth.callback');


Route::get('products', [ProductsController::class, 'list'])->name('products_list');
Route::get('products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::get('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
Route::post('/products/{productId}/purchase', [ProductsController::class, 'purchase'])->name('products.purchase');
Route::post('/products/{product}/like', [ProductsController::class, 'like'])->name('products.like');



Route::get('/', function () {
    return view('welcome');
});

Route::get('/multable', function (Request $request) {
    $j = $request->number??5;
    $msg = $request->msg;
    return view('multable', compact("j", "msg"));
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/test', function () {
    return view('test');
});


Route::get('/cryptography', function (Request $request) {
    $data = $request->input('data', 'Welcome to Cryptography');
    $action = $request->input('action', 'Encrypt');
    $result = '';
    $status = 'Failed';

    $key = 'thisisasecretkey'; // AES-128 key (16 bytes)
    $cipher = 'aes-128-ecb';

    if ($action === "Encrypt") {
        $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA);
        if ($encrypted !== false) {
            $result = base64_encode($encrypted);
            $status = 'Encrypted Successfully';
        }
    } elseif ($action === "Decrypt") {
        $decoded = base64_decode($data);
        $decrypted = openssl_decrypt($decoded, $cipher, $key, OPENSSL_RAW_DATA);
        if ($decrypted !== false) {
            $result = $decrypted;
            $status = 'Decrypted Successfully';
        }
    } elseif ($action === "Hash") {
        $hashed = hash('sha256', $data, true);
        $result = base64_encode($hashed);
        $status = 'Hashed Successfully';
    } elseif ($action === "Sign") {
        $path = storage_path('app/private/useremail@domain.com.pfx');
        $password = '12345678';
        $certificates = [];
        $pfx = file_get_contents($path);

        if (openssl_pkcs12_read($pfx, $certificates, $password)) {
            $privateKey = $certificates['pkey'];
            $signature = '';
            if (openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                $result = base64_encode($signature);
                $status = 'Signed Successfully';
            }
        }
    } elseif ($action === "Verify") {
        $signature = base64_decode($request->input('result'));
        $path = storage_path('app/public/loayService.localhost.com.crt');
        $publicKey = file_get_contents($path);

        if (openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1) {
            $status = 'Verified Successfully';
        } else {
            $status = 'Verification Failed';
        }
    } elseif ($action === "KeySend") {
        $path = storage_path('app/public/loayService.localhost.com.crt');
        $publicKey = file_get_contents($path);
        $encrypted = '';

        if (openssl_public_encrypt($data, $encrypted, $publicKey)) {
            $result = base64_encode($encrypted);
            $status = 'Key is Encrypted Successfully';
        }
    } elseif ($action === "KeyRecive") {
        $path = storage_path('app/private/useremail@domain.com.pfx');
        $password = '12345678';
        $certificates = [];
        $pfx = file_get_contents($path);

        if (openssl_pkcs12_read($pfx, $certificates, $password)) {
            $privateKey = $certificates['pkey'];
            $encryptedKey = base64_decode($data);
            $decrypted = '';

            if (openssl_private_decrypt($encryptedKey, $decrypted, $privateKey)) {
                $result = $decrypted;
                $status = 'Key is Decrypted Successfully';
            }
        }
    }

    return view('cryptography', compact('data', 'result', 'action', 'status'));
})->name('cryptography');





