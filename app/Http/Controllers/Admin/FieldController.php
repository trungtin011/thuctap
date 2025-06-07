<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use Illuminate\Support\Str;
use App\Models\Department; // Thêm model Department

class FieldController extends Controller
{
 public function index()
{
    $fields = Field::with('department')->get();
    return view('admin.truong.index', compact('fields'));
}


    public function create()
    {
        $departments = Department::all(); // Lấy tất cả phòng ban
        return view('admin.truong.create', compact('departments'));
    }

   public function store(Request $request)
{
    $request->validate([
        'fields' => 'required|array|min:1',
        'fields.*.name' => 'required|string|max:255|distinct',
        'fields.*.type' => 'required|in:text,number,email,date',
        'fields.*.department_id' => 'nullable|exists:departments,id',
    ]);

    foreach ($request->fields as $fieldData) {
        Field::create([
            'name' => $fieldData['name'],
            'slug' => \Str::slug($fieldData['name']),
            'type' => $fieldData['type'],
            'required' => false, // hoặc true nếu bạn cần
            'department_id' => $fieldData['department_id'] ?? null,
        ]);
    }

    return redirect()->route('admin.truong.fields.index')->with('success', 'Thêm các trường dữ liệu thành công!');
}
 public function edit($id)
    {
        $field = Field::findOrFail($id);
        $departments = Department::all();
        return view('admin.truong.edit', compact('field', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fields,name,' . $id,
            'type' => 'required|in:text,number,email,date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $field = Field::findOrFail($id);
        $field->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.truong.fields.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $field = Field::findOrFail($id);
        $field->delete();

        return redirect()->route('admin.truong.fields.index')->with('success', 'Xóa trường dữ liệu thành công!');
    }

}