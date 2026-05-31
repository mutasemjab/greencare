<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PledgeForm;

class PledgeFormController extends Controller
{
    public function index()
    {
        $pledgeForms = PledgeForm::with(['room', 'nurse'])->latest()->paginate(20);
        return view('admin.pledge-forms.index', compact('pledgeForms'));
    }

    public function show(PledgeForm $pledgeForm)
    {
        $pledgeForm->load(['room', 'nurse']);
        return view('admin.pledge-forms.show', compact('pledgeForm'));
    }
}
