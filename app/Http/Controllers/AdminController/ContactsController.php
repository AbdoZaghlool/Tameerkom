<?php

namespace App\Http\Controllers\AdminController;

use App\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Mail\ReplyMessage;
use App\Notifications\replay;
use Illuminate\Support\Facades\Mail;

class ContactsController extends Controller
{
    /**
     * grap all contacts methods to the admin
     *
     * @return void
     */
    public function index()
    {
        $contacts = Contact::latest()->get();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request)
    {
        try {
            $user = User::where('email', $request->receiver_email)->first();
            $user->notify(new replay($request->msg_body));
            $contact = Contact::find($request->id);
            $contact->update([
                'reply' => $request->msg_body
            ]);
            $v = '{"message":"done"}';
            return response()->json($v);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            $v = '{"error":"done"}';
            return response()->json($v);
        }
    }

    public function delete(Contact $contact)
    {
        $contact->delete();
        flash('تم الحذف')->success();
        return redirect('admin/contacts');
    }
}
