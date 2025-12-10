<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardNumber;
use App\Models\Category;
use App\Models\Doseyat;
use App\Models\POS;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Card::with(['pos', 'cardNumbers'])
            ->latest()
            ->paginate(10);

        return view('admin.cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posRecords = POS::orderBy('name')->get();
        return view('admin.cards.create', compact('posRecords',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pos_id' => 'nullable|exists:p_o_s,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'number_of_cards' => 'required|integer|min:1|max:10000',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('photo')) {
                $validated['photo'] = uploadImage('assets/admin/uploads', $request->photo);
            }
            
            // Create the card
            $card = Card::create($validated);

            // Generate card numbers
            $generatedNumbers = $card->generateCardNumbers();

            DB::commit();

            return redirect()->route('cards.index')
                ->with('success', __('messages.card_created_successfully') . ' ' . count($generatedNumbers) . ' ' . __('messages.card_numbers_generated'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_creating_card') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        $card->load(['pos', 'cardNumbers',]);
        return view('admin.cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {
        $posRecords = POS::orderBy('name')->get();
        return view('admin.cards.edit', compact('card', 'posRecords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'pos_id' => 'nullable|exists:p_o_s,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'number_of_cards' => 'required|integer|min:1|max:10000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $oldNumberOfCards = $card->number_of_cards;

            if ($request->hasFile('photo')) {
                if ($card->photo) {
                    $filePath = base_path('assets/admin/uploads/' . $card->photo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $validated['photo'] = uploadImage('assets/admin/uploads', $request->photo);
            }

            // Update the card
            $card->update($validated);

            // If number of cards changed, regenerate card numbers
            if ($oldNumberOfCards != $request->number_of_cards) {
                $generatedNumbers = $card->generateCardNumbers();
                $message = __('messages.card_updated_successfully') . ' ' . count($generatedNumbers) . ' ' . __('messages.card_numbers_regenerated');
            } else {
                $message = __('messages.card_updated_successfully');
            }

            DB::commit();

            return redirect()->route('cards.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_updating_card') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        try {
            $card->delete(); // This will also delete related card_numbers due to cascade

            return redirect()->route('cards.index')
                ->with('success', __('messages.card_deleted_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_card') . ': ' . $e->getMessage());
        }
    }

    /**
     * Regenerate card numbers for a specific card
     */
    public function regenerateNumbers(Card $card)
    {
        DB::beginTransaction();

        try {
            $generatedNumbers = $card->generateCardNumbers();

            DB::commit();

            return redirect()->back()
                ->with('success', count($generatedNumbers) . ' ' . __('messages.card_numbers_regenerated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_regenerating_numbers') . ': ' . $e->getMessage());
        }
    }

    /**
     * Show card numbers for a specific card with enhanced filtering
     */
    public function showNumbers(Request $request, Card $card)
    {
        $query = $card->cardNumbers()->with(['assignedUser']);

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'available':
                    $query->where('sell', CardNumber::SELL_NOT_SOLD)
                          ->whereNull('assigned_user_id')
                          ->where('status', CardNumber::STATUS_NOT_USED)
                          ->where('activate', CardNumber::ACTIVATE_ACTIVE);
                    break;
                case 'sold_not_assigned':
                    $query->where('sell', CardNumber::SELL_SOLD)
                          ->whereNull('assigned_user_id');
                    break;
                case 'sold_assigned':
                    $query->where('sell', CardNumber::SELL_SOLD)
                          ->whereNotNull('assigned_user_id')
                          ->where('status', CardNumber::STATUS_NOT_USED);
                    break;
                case 'used':
                    $query->where('status', CardNumber::STATUS_USED);
                    break;
            }
        }

        // Filter by activate status
        if ($request->filled('activate')) {
            $query->where('activate', $request->activate);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'LIKE', "%{$search}%")
                  ->orWhereHas('assignedUser', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%")
                               ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
            });
        }

        $cardNumbers = $query->latest()->paginate(20);

        return view('admin.cards.card-numbers', compact('card', 'cardNumbers'));
    }
}