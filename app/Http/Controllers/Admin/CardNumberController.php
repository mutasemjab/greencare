<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

use App\Models\Card;
use App\Models\CardNumber;
use App\Models\POS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CardNumberController extends Controller
{
    /**
     * Toggle the sell status of a card number
     */
    public function toggleSell(CardNumber $cardNumber)
    {
        try {
            if ($cardNumber->sell == CardNumber::SELL_NOT_SOLD) {
                // Mark as sold
                $cardNumber->markAsSold();
                $message = __('messages.card_number_marked_sold');
            } else {
                // Mark as not sold (reset to available)
                $cardNumber->markAsNotSold();
                $message = __('messages.card_number_marked_not_sold');
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_updating_sell_status') . ': ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign card number to user (updated)
     */
    public function showAssignForm(CardNumber $cardNumber)
    {
        // Allow assignment if card is sold (regardless of current assignment)
        if ($cardNumber->sell != CardNumber::SELL_SOLD) {
            return redirect()->back()->with('error', __('messages.card_must_be_sold_first'));
        }

        $users = User::orderBy('name')->get();
        return view('admin.card-numbers.assign', compact('cardNumber', 'users'));
    }

    /**
     * Assign card number to a user (updated)
     */
    public function assignToUser(Request $request, CardNumber $cardNumber)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            // Allow assignment if card is sold
            if ($cardNumber->sell != CardNumber::SELL_SOLD) {
                return redirect()->back()->with('error', __('messages.card_must_be_sold_first'));
            }

            $cardNumber->assignToUser($request->user_id);
            $user = User::find($request->user_id);

            return redirect()->back()->with('success', 
                __('messages.card_number_assigned_successfully', [
                    'number' => $cardNumber->number,
                    'user' => $user->name
                ])
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_assigning_card') . ': ' . $e->getMessage());
        }
    }

    /**
     * Mark card number as used
     */
    public function markAsUsed(CardNumber $cardNumber, Request $request)
    {
        try {
            $userId = $request->user_id ?? $cardNumber->assigned_user_id;
            
            if (!$userId) {
                return redirect()->back()->with('error', __('messages.user_required_for_usage'));
            }

            $cardNumber->markAsUsed($userId);

            return redirect()->back()->with('success', __('messages.card_number_marked_used_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_marking_used') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove user assignment (make card available again)
     */
    public function removeAssignment(CardNumber $cardNumber)
    {
        try {
            // Only allow if card is assigned but not used
            if (!$cardNumber->isAssignedButNotUsed()) {
                return redirect()->back()->with('error', __('messages.cannot_remove_assignment'));
            }

            $cardNumber->update(['assigned_user_id' => null]);

            return redirect()->back()->with('success', __('messages.assignment_removed_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_removing_assignment') . ': ' . $e->getMessage());
        }
    }

    /**
     * Search for users (AJAX endpoint)
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('phone', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'phone']);

        return response()->json($users);
    }

    /**
     * Toggle the status of a card number (used/not used)
     */
    public function toggleStatus(CardNumber $cardNumber)
    {
        try {
            // If marking as used, ensure user is assigned
            if ($cardNumber->status == CardNumber::STATUS_NOT_USED) {
                if (!$cardNumber->assigned_user_id) {
                    return redirect()->back()->with('error', __('messages.assign_user_before_marking_used'));
                }
                
                // Mark as used and create usage record
                $cardNumber->markAsUsed();
                $message = __('messages.card_number_marked_used');
            } else {
                // Mark as not used and remove usage records
                DB::transaction(function () use ($cardNumber) {
                    $cardNumber->update(['status' => CardNumber::STATUS_NOT_USED]);
                    $cardNumber->cardUsages()->delete();
                });
                $message = __('messages.card_number_marked_unused');
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_updating_status') . ': ' . $e->getMessage());
        }
    }

    /**
     * Toggle the activate status of a card number (active/inactive)
     */
    public function toggleActivate(CardNumber $cardNumber)
    {
        try {
            $newActivate = $cardNumber->activate == CardNumber::ACTIVATE_ACTIVE 
                ? CardNumber::ACTIVATE_INACTIVE 
                : CardNumber::ACTIVATE_ACTIVE;
            
            $cardNumber->update(['activate' => $newActivate]);
            
            $message = $newActivate == CardNumber::ACTIVATE_ACTIVE 
                ? __('messages.card_number_activated')
                : __('messages.card_number_deactivated');
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_updating_activate') . ': ' . $e->getMessage());
        }
    }

    /**
     * Bulk assign multiple card numbers to users
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'card_numbers' => 'required|array',
            'card_numbers.*' => 'exists:card_numbers,id',
            'assignments' => 'required|array',
            'assignments.*' => 'exists:users,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->card_numbers as $index => $cardNumberId) {
                    $cardNumber = CardNumber::find($cardNumberId);
                    $userId = $request->assignments[$index];
                    
                    if ($cardNumber && $cardNumber->isAvailable() && $userId) {
                        $cardNumber->assignToUser($userId);
                    }
                }
            });

            return redirect()->back()->with('success', __('messages.bulk_assignment_successful'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_bulk_assignment') . ': ' . $e->getMessage());
        }
    }
}