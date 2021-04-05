<?php

namespace App\Http\Controllers;

use File;
use DataTables;
use App\Product;
use App\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with('category');
            if($request->get('name') != ''){
                $data = $data->where('name', 'like', '%'.$request->get('name').'%');
            }
            if($request->get('category') != ''){
                $data = $data->where('category_id', $request->get('category'));
            }
            $data = $data->latest()->get();
            //echo '<pre>';print_r($data->toArray());echo '</pre>';exit();
            return Datatables::of($data)
                    ->addColumn('checkbox', function($row){

                        $chk = '<input type="checkbox" name="chkProduct" value="'.$row->id.'">';

                        return $chk;
                    })
                    ->addColumn('action', function($row){

                        $btn = '<a href="'.route('products.edit', $row->id).'" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';

                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';

                        return $btn;
                    })
                    ->editColumn('category', function($row) {
                        if(isset($row->category->name)){
                            return $row->category->name;
                        }else{
                            return '-';
                        }
                    })
                    ->editColumn('image', function($row) {
                        return '<img src="'.asset('uploads/thumbnail/'.$row->image).'">';
                    })
                    ->rawColumns(['checkbox','action','category','image'])
                    ->make(true);
        }
        
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'category' => 'required',
            'image' => 'required|image|dimensions:min_width=250,min_height=250',
        ]);

        $product = new Product;
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->category_id = $request->get('category');

        $file_path = '';
        if($request->get('base64Image')!=''){

            $image_parts = explode(";base64,", $request->get('base64Image'));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image = time() . '.' . $image_type;
            $file_path = public_path('uploads') . '/' . $image;

            file_put_contents($file_path, $image_base64);
            $product->image = $image;
        }else{

            if ($files = $request->file('image')){
                $image = time() . "." . $files->getClientOriginalExtension();
                $file_path = public_path('uploads') . '/' . $image;
                $files->move(public_path('uploads'), $image);   
                $product->image = $image;
            }
        }
        if($file_path != ''){
            //create thumbnail
            $thumbnail_path = public_path('uploads/thumbnail/'.$image);
            File::copy($file_path, public_path('uploads/thumbnail/'.$image));
            $this->__createThumbnail($thumbnail_path, 150, 150);
        }

        $product->save();

        return redirect()->route('products.index')
                        ->with('success','Product has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('product.edit',compact('product'));
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
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'category' => 'required',
            'image' => 'nullable|image|dimensions:min_width=250,min_height=250',
        ]);

        echo '<pre>'; print_r($request->all()); exit();
        $product = Product::findOrFail($id);
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->category_id = $request->get('category');

        $file_path = '';
        if($request->get('base64Image')!=''){

            $image_parts = explode(";base64,", $request->get('base64Image'));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image = time() . '.' . $image_type;
            $file_path = public_path('uploads') . '/' . $image;

            file_put_contents($file_path, $image_base64);
            $product->image = $image;
        }else{

            if ($files = $request->file('image')){
                $image = time() . "." . $files->getClientOriginalExtension();
                $file_path = public_path('uploads') . '/' . $image;
                $files->move(public_path('uploads'), $image);   
                $product->image = $image;
            }
        }
        if($file_path != ''){
            //create thumbnail
            $thumbnail_path = public_path('uploads/thumbnail/'.$image);
            File::copy($file_path, public_path('uploads/thumbnail/'.$image));
            $this->__createThumbnail($thumbnail_path, 150, 150);
        }

        $product->save();

        return redirect()->route('products.index')
                        ->with('success','Product has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    
        return response()->json(['status' => 200, 'msg' => 'Product has been deleted.']);
    }

    public function destroyAll(Request $request)
    {
        Product::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 200, 'msg' => 'Product(s) has been deleted.']);
    }
}
