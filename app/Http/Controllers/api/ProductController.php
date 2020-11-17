<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AttachmentsImage;
use Validator;

class ProductController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return Product::with('images')->get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'article' => 'required|unique:products',
			'price' => 'required|min:0',
			'description' => 'required',			
			'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);	

		if ($validator->fails()) {
			return response()->json([
				'status' => 'error',
				'errors' => $validator->errors()->messages()
			], 200);
		}

		\DB::transaction(function () use ($request) {
			$post = Product::create($request->only(['name', 'description', 'price', 'article']));

			$images = $request->files;
			foreach ($images as $image) {
				$imagePath = \Storage::putFile('/uploads/products', $image);

				AttachmentsImage::create([
					'image_path' => $imagePath,
					'product_id' => $post->id
				]);
			}
		});

		return response()->json(200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		return Product::where('id', $id)->with('images')->get();
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
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			// 'article' => 'required|unique:products',
			'price' => 'required|min:0',
			'description' => 'required',			
			'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);	

		if ($validator->fails()) {
			return response()->json([
				'status' => 'error',
				'errors' => $validator->errors()->messages()
			], 200);
		}

		\DB::transaction(function () use ($request, $id) {
			$post = Product::findOrFail($id);		
			$post->update($request->only(['name', 'description', 'price', 'article']));

			$imagesProduct = AttachmentsImage::where('product_id', $post->id)->get();

			foreach ($imagesProduct as $image) {
				\Storage::delete($image->image_path);
				$image->delete();
			}			

			// $imagesProduct->delete();

			$images = $request->files;
			foreach ($images as $image) {
				$imagePath = \Storage::putFile('/uploads/products', $image);

				AttachmentsImage::create([
					'image_path' => $imagePath,
					'product_id' => $post->id
				]);
			}
		});

		return response()->json(200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$user = Product::findOrFail($id);
		$user->delete();
		return response()->json([
			'message' => 'Product deleted successfully'
		]);
	}
}
