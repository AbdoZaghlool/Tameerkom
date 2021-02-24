<?php

namespace App\Http\Controllers\AdminController;

use App\Complaint;
use App\Http\Controllers\Controller;

class ComplaintController extends Controller
{
    /**
     * list all complaints from users
     *
     * @return view
     */
    public function index()
    {
        $complaints = Complaint::with('order', 'order.user')->latest()->get();
        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * delete specific complaint from storage
     *
     * @param Complaint $id
     * @return void
     */
    public function delete($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();
        flash('تم حذف الشكوى بنجاح');
        return back();
    }
}