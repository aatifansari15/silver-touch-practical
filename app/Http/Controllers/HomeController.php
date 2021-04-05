<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    protected function getCategoriesDropDown($id=null) 
    {
        $result = [];
        $categories = Category::select('id','name','parent_id');
        if($id!=null){
            $categories = $categories->where('id', '!=', $id);    
        }
        $categories = $categories->get();
        if($categories->isNotEmpty()){
            $ref   = [];
            $items = [];
            foreach ($categories as $key => $value) {
                
                $thisRef = &$ref[$value->id];

                $thisRef['id'] = $value->id;
                $thisRef['name'] = $value->name;
                $thisRef['parent_id'] = $value->parent_id;
                
                if($value->parent_id == 0) {
                    $items[$value->id] = &$thisRef;
                } else {
                    $ref[$value->parent_id]['child'][$value->id] = &$thisRef;
                }
            }
            $result = $this->generateCategoriesOptions('', $items);
        }
        return response()->json(['categories'=>$result]);
    }

    protected function generateCategoriesOptions($prefix, $items)
    {
        $str = '';
        $span = '<span>—</span>';
        foreach($items as $key=>$value) {
            $str .= '<option value="'.$value['id'].'">'.$prefix.$value['name'].'</option>';                 
            if(array_key_exists('child',$value)) {
                $str .= $this->generateCategoriesOptions($prefix.$span, $value['child'],'child');
            }
            
        }
        return $str;
    }
}
