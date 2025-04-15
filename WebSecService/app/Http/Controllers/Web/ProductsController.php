<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\like;




class ProductsController extends Controller {

	use ValidatesRequests;

	public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

	public function list(Request $request)
{
    $query = Product::select("products.*");
    $query->when($request->keywords, fn($q) => $q->where("name", "like", "%$request->keywords%"));
    $query->when($request->min_price, fn($q) => $q->where("price", ">=", $request->min_price));
    $query->when($request->max_price, fn($q) => $q->where("price", "<=", $request->max_price));
    $query->when($request->order_by, function ($q) use ($request) {
        if ($request->order_by == "popularity") {
            $q->orderBy("likes", $request->order_direction ?? "DESC");
        } else {
            $q->orderBy($request->order_by, $request->order_direction ?? "ASC");
        }
    });
    $products = $query->get();
    return view('products.list', compact('products'));
}


	public function edit(Request $request, Product $product = null) {

		if(!auth()->user()) return redirect('/');

		$product = $product??new Product();

		return view('products.edit', compact('product'));
	}

	public function save(Request $request, Product $product = null) {

		$this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
	        'description' => ['required', 'string', 'max:1024'],
	        'price' => ['required', 'numeric'],
	    ]);

		$product = $product??new Product();
		$product->fill($request->all());
		$product->save();

		return redirect()->route('products_list');
	}

	public function delete(Request $request, Product $product) {

		if(!auth()->user()->hasPermissionTo('delete_products')) abort(401);

		$product->delete();

		return redirect()->route('products_list')->with('success', 'Delete successful.');
	}



	public function purchase(Request $request, $productId)
{
    $product = Product::findOrFail($productId);
    
    $totalPrice = $product->price;

    // تأكد أن المستخدم لديه رصيد كافٍ قبل إتمام عملية الشراء
    $user = auth()->user();

    if ($user->credit < $totalPrice) {
        // عرض رسالة تفيد بعدم كفاية الرصيد
        return redirect()->back()->with('error', 'You do not have enough credit to make this purchase.');
    }

    // خصم الرصيد من المستخدم
    $user->credit -= $totalPrice;
    $user->save();

    // سجل عملية الشراء في جدول purchases
    Purchase::create([
        'user_id' => $user->id,
        'product_id' => $productId,
        'total_price' => $totalPrice,
    ]);

    // عرض رسالة تفيد بنجاح عملية الشراء
    return redirect()->route('products_list')->with('success', 'Purchase successful.');
}


public function like(Product $product)
{
    $product->likes++;
    $product->save();
	return redirect()->route('products_list');

}



} 