<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Infos;
use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{

    public function __construct()
    {

        $this->middleware(['auth','role:admin'])->except('show');

    }

    public function index()
    {
        //
        $categories = Category::all();
        $tags = Tag::all();
        $infos = Infos::paginate(1);
        $products = Product::query()->with('category')->paginate(6);
        return view('products.index', compact('products', 'categories','infos','tags'));
    }


    public function create()
    {

        $product = new Product();
        $categories = Category::all();
        $brands = Brand::all();
        $colors=Color::all();
        $sizes=Size::all();
        $tags=Tag::all();

        $product->fill([
            'quantity' => 0,
        ]);

        $isUpdate = false;
        return view('products.form', compact('product', 'isUpdate', 'categories','brands','colors','sizes','tags'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required',
            'price' => 'nullable',
            'description' => 'nullable',
            'old_price' => 'required',
            'sold' => 'required',
            'quantity' => 'required',
            'status' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'colors.*' => 'nullable|string|max:255',
            'sizes.*' => 'nullable|string|max:255',
            'tags.*' => 'nullable|string|max:255',

        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $imagePath = $image->store('images', 'public');
                $imagePaths[] = $imagePath;
            }

            // Add the image paths to the validated data
            $validatedData['images'] = json_encode($imagePaths);
        }


        $Product = Product::create($validatedData);

        if ($request->filled('colors')) {
            $colors = $request->input('colors');
            $Product->colors()->attach($colors);
        }

        if ($request->filled('sizes')) {
            $sizes = $request->input('sizes');
            $Product->sizes()->attach($sizes);
        }

        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $Product->tags()->attach($tags);
        }

        Alert::success('succes', 'Product has been added successfully');
        return to_route('products.index');


        // $fromFields = $request->validated();
        // if ($request->hasFile('image')) {
        //     // $fromFields['image'] = $request->file('image')->store('product' , 'public');
        //     $fromFields['image']=$this->uploadImage($request);
        // }

        // Product::create($fromFields);




    }

    /**
     * Display the specified resource.
     */
   public function show(Request $request ,string $id)
    {

        $product = Product::findOrFail($id);
        $totalCartCount = 0; // Default value
        if ($request->user()) {
            $totalCartCount = $request->user()->cartItems()->count();
        }
        $Tag = Tag::all();
        $tags = Tag::all();
        $products = Product::all();
        $product->load('colors'); // BOUCLE
        $product->load('sizes');
        $new_products = Product::paginate(2);
        $infos = Infos::paginate(1);
        $categories = Category::all();
        $product->load('tags');
        // $totalCartCount = $request->user()->cartItems()->count();
        $Reviews = Review::where('product_id', $id)->get();
        $productsWithReviewCount = Review::where('product_id', $id)->count();
        return view('products.show', compact('product','categories','infos','Reviews','productsWithReviewCount','new_products','Tag','products','totalCartCount','tags'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {

        $isUpdate = true;
        $tags=Tag::all();
        $categories = Category::all();
        $brands = Brand::all();
        $colors=Color::all();
        $sizes=Size::all();

        return view('products.form', compact('product', 'isUpdate', 'categories', 'brands','colors','sizes','tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $product=Product::findOrFail($id);
        // $validatedData = $request->validate();

        // if($request->hasFile('image')) {
        //     $photoPath1 = $request->file('image')->store('Products','public');
        //     $validatedData['image']=$photoPath1;
        // }
        // $product->update($validatedData);

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required',
            'price' => 'nullable',
            'description' => 'nullable',
            'old_price' => 'required',
            'sold' => 'required',
            'quantity' => 'required',
            'status' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'colors.*' => 'nullable|string|max:255',
            'sizes.*' => 'nullable|string|max:255',
            'tags.*' => 'nullable|string|max:255',

        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $imagePath = $image->store('images', 'public');
                $imagePaths[] = $imagePath;
            }

            // Add the image paths to the validated data
            $validatedData['images'] = json_encode($imagePaths);
        }

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        if ($request->filled('colors')) {
            $colors = $request->input('colors');
            $product->colors()->sync($colors);
        }

        // Sync sizes to the product, if provided
        if ($request->filled('sizes')) {
            $sizes = $request->input('sizes');
            $product->sizes()->sync($sizes);
        }

        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $product->tags()->attach($tags);
        }

        Alert::success('Successfully Updated!', "The product {$product->name} has been updated");
        return to_route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
        $product->delete();
        Alert::error('Deleted!', "The product {$product->name} has been deleted");
        return to_route('products.index');
    }
}



