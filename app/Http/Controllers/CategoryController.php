<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::where('is_active', true);

        // 1. Lọc theo Chủng loại (CPU, RAM, Mainboard...)
        if ($request->filled('type')) {
            $query->where('category_type', $request->type);
        }

        // 2. Lọc theo Thương hiệu (Intel, AMD, Asus...)
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // 3. Lọc theo Socket / Chuẩn kết nối (LGA 1700, AM5, DDR4...)
        if ($request->filled('socket')) {
            $query->where('socket_type', $request->socket);
        }

        $categories = $query->latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Bạn không có quyền thêm dữ liệu!');
        }

        return view('categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Bạn không có quyền thực hiện hành động này!');
        }

        // Ràng buộc dữ liệu (Validation) - Bổ sung đầy đủ các trường linh kiện
        $validated = $request->validate([
            'code'            => 'nullable|string|max:50|unique:categories,code',
            'name'            => 'required|string|max:255|unique:categories,name',
            'category_type'   => 'nullable|string|max:100',
            'brand'           => 'required|string|max:100',
            'socket_type'     => 'nullable|string|max:100',
            'specifications' => 'required|string',
            'price'           => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'warranty_months' => 'nullable|integer|min:0',
            'condition'       => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'name.required'           => 'Vui lòng nhập tên linh kiện!',
            'name.unique'             => 'Linh kiện này đã tồn tại trong hệ thống!',
            'code.unique'             => 'Mã SKU này đã tồn tại!',
            'specifications.required' => 'Vui lòng nhập thông số kỹ thuật!',
            'brand.required'          => 'Vui lòng chọn hoặc nhập thương hiệu!',
            'price.required'          => 'Vui lòng nhập giá bán!',
            'price.numeric'           => 'Giá bán phải là chữ số!',
            'price.min'               => 'Giá bán không được là số âm!',
            'stock.required'          => 'Vui lòng nhập số lượng tồn kho!',
            'stock.integer'           => 'Số lượng tồn kho phải là số nguyên!',
            'stock.min'               => 'Số lượng tồn kho không được là số âm!',
            'image.image'             => 'File tải lên phải là hình ảnh!',
            'image.mimes'             => 'Ảnh phải thuộc định dạng: jpeg, png, jpg, gif, webp!',
            'image.max'               => 'Dung lượng ảnh không được vượt quá 5MB!',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads'), $imageName);
            $validated['image'] = 'uploads/' . $imageName;
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Thêm mới linh kiện thành công!');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Bạn không có quyền sửa dữ liệu!');
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Bạn không có quyền cập nhật dữ liệu!');
        }

        $validated = $request->validate([
            'code'            => ['nullable', 'string', 'max:50', Rule::unique('categories', 'code')->ignore($category->id)],
            'name'            => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'category_type'   => 'nullable|string|max:100',
            'brand'           => 'required|string|max:100',
            'socket_type'     => 'nullable|string|max:100',
            'specifications' => 'required|string',
            'price'           => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'warranty_months' => 'nullable|integer|min:0',
            'condition'       => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'name.required'           => 'Vui lòng nhập tên linh kiện!',
            'name.unique'             => 'Linh kiện này đã tồn tại trong hệ thống!',
            'code.unique'             => 'Mã SKU này đã tồn tại!',
            'specifications.required' => 'Vui lòng nhập thông số kỹ thuật!',
            'brand.required'          => 'Vui lòng chọn hoặc nhập thương hiệu!',
            'price.required'          => 'Vui lòng nhập giá bán!',
            'price.numeric'           => 'Giá bán phải là chữ số!',
            'price.min'               => 'Giá bán không được là số âm!',
            'stock.required'          => 'Vui lòng nhập số lượng tồn kho!',
            'stock.integer'           => 'Số lượng tồn kho phải là số nguyên!',
            'stock.min'               => 'Số lượng tồn kho không được là số âm!',
            'image.image'             => 'File tải lên phải là hình ảnh!',
            'image.mimes'             => 'Ảnh phải thuộc định dạng: jpeg, png, jpg, gif, webp!',
            'image.max'               => 'Dung lượng ảnh không được vượt quá 5MB!',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }

            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads'), $imageName);
            $validated['image'] = 'uploads/' . $imageName;
        }

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Cập nhật linh kiện thành công!');
    }

    public function destroy(Category $category)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')->with('error', 'Bạn không có quyền xóa dữ liệu!');
        }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Xóa linh kiện thành công!');
    }
}