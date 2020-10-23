<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\API\ApiError;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  private $product;

  public function __construct(Product $product)
  {
    $this->product = $product;
  }

  public function index()
  {
    return response()->json($this->product->paginate(10));
  }

  public function show($id)
  {
    $product = $this->product->find($id);

    if (!$product) {
      return response()->json(ApiError::errorMessage('Product not found', 1040), 404);
    }

    return response()->json(['data' => $product]);
  }

  public function store(Request $request)
  {
    try {
      $productData = $request->all();
      $productCreated = $this->product->create($productData);
      $return = ['data' => ['message' => 'Product created', 'product' => $productCreated]];

      return response()->json($return, 201);
    } catch (\Exception $e) {
      if (config('app.debug')) {
        return response()->json(ApiError::errorMessage($e->getMessage(), 1001), 500);
      }
      return response()->json(ApiError::errorMessage('Store error', 1001), 500);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $productData = $request->all();
      $product = $this->product->find($id);
      $product->update($productData);
      $return = ['data' => ['message' => 'Product updated']];

      return response()->json($return, 201);
    } catch (\Exception $e) {
      if (config('app.debug')) {
        return response()->json(ApiError::errorMessage($e->getMessage(), 1002), 500);
      }
      return response()->json(ApiError::errorMessage('Update error', 1002), 500);
    }
  }

  public function delete(Product $id)
  {
    try {
      $id->delete();
      $return = ['data' => ['message' => 'Product '.$id->name.' deleted']];

      return response()->json($return, 200);
    } catch (\Exception $e) {
      if (config('app.debug')) {
        return response()->json(ApiError::errorMessage($e->getMessage(), 1003), 500);
      }
      return response()->json(ApiError::errorMessage('Delete error', 1003), 500);
    }
  }
}
