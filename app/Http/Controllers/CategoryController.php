<?php

namespace App\Http\Controllers;

use File;
use DataTables;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $categories = $this->getCategories();
        return view('category.index',compact('categories'));
    }

    protected function getCategories($level = NULL, $prefix = '') 
    {
        $span = '<span>—</span>';
        $categories = Category::where('parent_id', $level)->get();
        $category_list = '';
        if ($categories->isNotEmpty()) {
            foreach ($categories as $key => $category) {

                $category_list .= '<tr id="row_'.$category->id.'">';
                    $category_list .= '<td class="text-center"><input type="checkbox" name="chkProduct" value="'.$category->id.'"></td>';
                    $category_list .= '<td>'.$prefix.$category->name.'</td>';
                    $category_list .= '<td><img src="'.asset('uploads/thumbnail/'.$category->image).'"></td>';
                    $category_list .= '<td class="action">';
                        $category_list .= '<form method="POST" id="deletefrm_'.$category->id.'" action="'.route('categories.destroy', $category->id).'">';
                            $category_list .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
                            $category_list .= '<input type="hidden" name="_method" value="DELETE">';
                            $category_list .= '<a href="'.route('categories.edit', $category->id).'" data-toggle="tooltip" data-id="'.$category->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editCategory">Edit</a>';
                            $category_list .= ' <a href="javascript:void(0)" onclick="deleteRow('.$category->id.')" data-toggle="tooltip" data-original-title="Delete" class="btn btn-danger btn-sm deleteCategory">Delete</a>';
                        $category_list .= '</form>';
                    $category_list .= '</td>';
                $category_list .= '</tr>';
                
                $category_list .= $this->getCategories($category->id, $prefix . $span);
            }
        }
        return $category_list;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
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
            'image' => 'required|image|dimensions:min_width=250,min_height=250',
        ]);

        $category = new Category;
        $category->name = $request->get('name');
        $category->parent_id = $request->get('parent_id');

        $file_path = '';
        if($request->get('base64Image')!=''){

            $image_parts = explode(";base64,", $request->get('base64Image'));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image = time() . '.' . $image_type;
            $file_path = public_path('uploads') . '/' . $image;

            file_put_contents($file_path, $image_base64);
            $category->image = $image;
        }else{

            if ($files = $request->file('image')){
                $image = time() . "." . $files->getClientOriginalExtension();
                $file_path = public_path('uploads') . '/' . $image;
                $files->move(public_path('uploads'), $image);   
                $category->image = $image;
            }
        }
        if($file_path != ''){
            //create thumbnail
            $thumbnail_path = public_path('uploads/thumbnail/'.$image);
            File::copy($file_path, public_path('uploads/thumbnail/'.$image));
            $this->__createThumbnail($thumbnail_path, 150, 150);
        }
        $category->save();

        return redirect()->route('categories.index')
                        ->with('success','Category has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('category.edit',compact('category'));
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
            'image' => 'nullable|image|dimensions:min_width=250,min_height=250',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->get('name');
        $category->parent_id = $request->get('parent_id');

        $file_path = '';
        if($request->get('base64Image')!=''){

            $image_parts = explode(";base64,", $request->get('base64Image'));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image = time() . '.' . $image_type;
            $file_path = public_path('uploads') . '/' . $image;

            file_put_contents($file_path, $image_base64);
            $category->image = $image;
        }else{

            if ($files = $request->file('image')){
                $image = time() . "." . $files->getClientOriginalExtension();
                $file_path = public_path('uploads') . '/' . $image;
                $files->move(public_path('uploads'), $image);   
                $category->image = $image;
            }
        }
        if($file_path != ''){
            //create thumbnail
            $thumbnail_path = public_path('uploads/thumbnail/'.$image);
            File::copy($file_path, public_path('uploads/thumbnail/'.$image));
            $this->__createThumbnail($thumbnail_path, 150, 150);
        }

        $category->save();

        return redirect()->route('categories.index')
                        ->with('success','Category has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Getting the parent category
        $parent = Category::findOrFail($id);
        // Getting all children ids
        $array_of_ids = $this->getChildren($parent);
        // Appending the parent category id
        array_push($array_of_ids, $id);

        foreach ($parent->products as $product) {
            $product->update(['category_id' => NULL]);
        }

        // Destroying all of them
        $cat_images = Category::whereIn('id',$array_of_ids)->get()->pluck('image');
        $this->__removeFileFromFolder($cat_images);
        //echo '<pre>';print_r($array_of_ids);echo '</pre>';exit();
        Category::destroy($array_of_ids);

        return redirect()->route('categories.index')
                        ->with('success','Category has been deleted.');
    }

    public function destroyAll(Request $request)
    {
        $ids = explode(',', $request->get('ids'));
        foreach ($ids as $id) {

            // Getting the parent category
            $parent = Category::find($id);
            // Getting all children ids
            $array_of_ids = $this->getChildren($parent);
            // Appending the parent category id
            array_push($array_of_ids, $id);
            
            if($parent){
                foreach ($parent->products as $product) {
                    $product->update(['category_id' => NULL]);
                }
            }

            // Destroying all of them
            $cat_images = Category::whereIn('id',$array_of_ids)->get()->pluck('image');
            $this->__removeFileFromFolder($cat_images);
            Category::destroy($array_of_ids);
        }
        return redirect()->route('categories.index')
                        ->with('success','Category has been deleted.');
    }

    private function getChildren($category){
        $ids = [];
        if($category){
            foreach ($category->children as $cat) {
                foreach ($cat->products as $product) {
                    $product->update(['category_id' => NULL]);
                }
                $ids[] = $cat->id;
                $ids = array_merge($ids, $this->getChildren($cat));
            }
        }
        return $ids;
    }
}
