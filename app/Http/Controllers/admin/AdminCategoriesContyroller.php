<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Upload_Images;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCategoriesContyroller extends Controller
{

    protected $uploadImages;

    public function __construct(Upload_Images $uploadImages)
    {
        $this->uploadImages = $uploadImages;
    }


    public function index()
    {

        $categories = Category::withCount('products')
            ->whereNull('parent_id')
            ->where('active_flag', '1')
            ->orderBy('categorie')
            ->get();

        $mostActiveCategory = Category::withCount('products')
            ->orderByDesc('products_count')
            ->first();


        $stats = [
            'total'           => Category::count(),
            'main'            => Category::whereNull('parent_id')->count(),
            'sub'             => Category::whereNotNull('parent_id')->count(),
            'most_active_name' => $mostActiveCategory?->categorie ?? '—',
            'most_active_ads'  => $mostActiveCategory?->products_count ?? 0,
        ];

        return view('admin.category.index', compact('categories', 'stats'));
    }

    public function addcategory()
    {
        return view('admin.category.add_edit_category');
    }

    public function addcategorySubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
        ]);

        $file = $request->file('icon');
        $file_path = $this->uploadImages->storageFilepath($file);

        $data = [
            "categorie" => $request->name,
            "category_icon" => $file_path,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('categories')->insert($data);
        if ($result) {
            return redirect('_admin/secure/categories')->with('success', 'Inserted successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function editcategory($id)
    {
        $category = Category::where('id', $id)->first();
        return view('admin.category.add_edit_category', ['data' => $category]);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();
        $request->validate([
            'name' => 'required',
        ]);

        $file = $request->file('icon');
        if ($file) {
            $file_path = $this->uploadImages->storageFilepath($file);
        } else {
            $file_path = $category->category_icon;
        }

        $data = [
            "categorie" => $request->name,
            "category_icon" => $file_path,
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('categories')->where('id', $id)->Update($data);
        if ($result) {
            return redirect('_admin/secure/categories')->with('success', 'Updated successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function deleteCategory($id)
    {

        $result = DB::table('categories')->where('id', $id)->Update(['active_flag' => '0']);
        if ($result) {
            return redirect('_admin/secure/categories')->with('success', 'Removed successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    // specification

    public function specificationIndex($id)
    {
        $category = Category::where('id', $id)->first();

        $data = DB::table('specifications')->where('category_id', $id)->where('active_flag', '1')->get();

        return view('admin.category.specifaction.index', ['data' => $data, 'category' => $category]);
    }

    public function specificationAdd($id)
    {
        $category = Category::where('id', $id)->first();
        return view('admin.category.specifaction.add_edit_specifaction', ['category' => $category]);
    }

    public function addSpecification(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();
        $request->validate([
            'specification' => 'required',
        ]);

        $data = [
            "category_id" => $id,
            "specification" => $request->specification,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('specifications')->insert($data);
        if ($result) {
            return redirect('_admin/secure/categories/specification/' . $id)->with('success', 'Inserted successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function specificationedit($id)
    {
        $data = DB::table('specifications')->where('id', $id)->first();
        return view('admin.category.specifaction.add_edit_specifaction', ['data' => $data]);
    }

    public function editSpecification(Request $request, $id)
    {
        $spec = DB::table('specifications')->where('id', $id)->first();
        $request->validate([
            'specification' => 'required',
        ]);

        $data = [
            "specification" => $request->specification,
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('specifications')->where('id', $id)->update($data);
        if ($result) {
            return redirect('_admin/secure/categories/specification/' . $spec->category_id)->with('success', 'Upadted successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function delete($id)
    {
        $spec = DB::table('specifications')->where('id', $id)->first();
        $result = DB::table('specifications')->where('id', $id)->update(['active_flag' => '0']);
        if ($result) {
            return redirect('_admin/secure/categories/specification/' . $spec->category_id)->with('success', 'Removed successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }


    // subcategories

    public function subcategories($id)
    {
        $data = Category::withCount('products')
            ->where('parent_id', $id)
            ->where('active_flag', '1')
            ->get();
        $category = Category::where('id', $id)->first();

        return view('admin.category.subcategory.index', ['data' => $data, 'category' => $category]);
    }

    public function subcategoriesAdd($id)
    {
        return view('admin.category.subcategory.add_edit_sub_category', ['id' => $id]);
    }


    public function addSubcategorysubmit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
        ]);

        $file = $request->file('icon');
        $file_path = $this->uploadImages->storageFilepath($file);

        $data = [
            "parent_id" => $id,
            "sub_categorie" => $request->name,
            "category_icon" => $file_path,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('categories')->insert($data);
        if ($result) {
            return redirect('_admin/secure/categories/sub/' . $id)->with('success', 'Inserted successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function subcategoriesedit($id)
    {

        $data = Category::where('id', $id)->first();

        return view('admin.category.subcategory.add_edit_sub_category', ['data' => $data]);
    }

    public function subcategoryeditSubmit(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();
        $request->validate([
            'name' => 'required',
        ]);

        $file = $request->file('icon');
        if ($file) {
            $file_path = $this->uploadImages->storageFilepath($file);
        } else {
            $file_path = $category->category_icon;
        }

        $data = [
            "sub_categorie" => $request->name,
            "category_icon" => $file_path,
            "updated_at" => Carbon::now()
        ];

        $result = DB::table('categories')->where('id', $id)->Update($data);
        if ($result) {
            return redirect('_admin/secure/categories/sub/' . $category->parent_id)->with('success', 'Updated successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }

    public function deletesubcategory($id)
    {
        $category = Category::where('id', $id)->first();
        $result = DB::table('categories')->where('id', $id)->Update(['active_flag' => 0]);
        if ($result) {
            return redirect('_admin/secure/categories/sub/' . $category->parent_id)->with('success', 'Updated successfully');
        } else {
            return redirect()->back()->with('error', 'Please try again');
        }
    }
}
