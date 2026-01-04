<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use App\Traits\SendsOrderNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use SendsOrderNotifications;

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with([
                'user:id,name,phone,email',
                'address',
                'orderProducts.product'
            ])->orderBy('created_at', 'desc');

            // Filter by order status
            if ($request->filled('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            // Filter by payment status
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by payment type
            if ($request->filled('payment_type')) {
                $query->where('payment_type', $request->payment_type);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('date', '<=', $request->to_date);
            }

            // Search by order number or user name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            $orders = $query->paginate(15);

            // Add status labels and statistics
            $orders->getCollection()->transform(function ($order) {
                $order->order_status_label = $this->getOrderStatusLabel($order->order_status);
                $order->payment_status_label = $this->getPaymentStatusLabel($order->payment_status);
                $order->items_count = $order->orderProducts->sum('quantity');
                return $order;
            });

            // Get statistics
            $statistics = $this->getOrderStatistics();

            return view('admin.orders.index', compact('orders', 'statistics'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load orders: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        try {
            $order = Order::with([
                'user:id,name,phone,email,',
                'address',
                'orderProducts.product'
            ])->findOrFail($id);

            $order->order_status_label = $this->getOrderStatusLabel($order->order_status);
            $order->payment_status_label = $this->getPaymentStatusLabel($order->payment_status);

            return view('admin.orders.show', compact('order'));

        } catch (\Exception $e) {
            return back()->with('error', 'Order not found: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit($id)
    {
        try {
            $order = Order::with([
                'user:id,name,phone,email',
                'address',
                'orderProducts.product'
            ])->findOrFail($id);

            $users = User::where('activate', 1)->get(['id', 'name', 'phone', 'email']);
            
            return view('admin.orders.edit', compact('order', 'users'));

        } catch (\Exception $e) {
            return back()->with('error', 'Order not found: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'order_status' => 'required|integer|in:1,2,3,4,5,6',
                'payment_status' => 'required|integer|in:1,2',
                'note' => 'nullable|string|max:500',
            ]);

            $order = Order::with('orderProducts', 'user')->findOrFail($id);
            $oldStatus = $order->order_status;
            $oldPaymentStatus = $order->payment_status;

            DB::beginTransaction();

            try {
                // Update order
                $order->update([
                    'order_status' => $request->order_status,
                    'payment_status' => $request->payment_status,
                    'note' => $request->note
                ]);

                // ============================================
                // Send notifications
                // ============================================
                
                // Send order status change notification
                if ($oldStatus != $request->order_status) {
                    $this->sendOrderStatusNotification($order, $oldStatus, $request->order_status);
                }

                // Send payment status notification
                if ($oldPaymentStatus != $request->payment_status) {
                    $this->sendPaymentStatusNotification($order, $request->payment_status);
                }

                // ============================================

                DB::commit();

                $message = 'Order updated successfully';
                if ($request->order_status == 4 && $oldStatus != 4) {
                    $message .= ' and customer notified about delivery.';
                }

                return redirect()->route('orders.show', $order->id)
                    ->with('success', $message);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified order
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Only allow deletion of pending or canceled orders
            if (!in_array($order->order_status, [1, 5])) {
                return back()->with('error', 'Only pending or canceled orders can be deleted.');
            }

            $order->delete();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    private function getOrderStatistics()
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 1)->count(),
            'accepted_orders' => Order::where('order_status', 2)->count(),
            'on_the_way_orders' => Order::where('order_status', 3)->count(),
            'delivered_orders' => Order::where('order_status', 4)->count(),
            'canceled_orders' => Order::where('order_status', 5)->count(),
            'refund_orders' => Order::where('order_status', 6)->count(),
            'total_revenue' => Order::where('order_status', 4)
                                   ->where('payment_status', 1)
                                   ->sum('total_prices'),
            'unpaid_orders' => Order::where('payment_status', 2)->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'this_month_orders' => Order::whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                                   ->where('order_status', 4)
                                   ->where('payment_status', 1)
                                   ->sum('total_prices'),
        ];
    }

    /**
     * Get order status label
     */
    private function getOrderStatusLabel($status)
    {
        $labels = [
            1 => 'Pending',
            2 => 'Accepted', 
            3 => 'On The Way',
            4 => 'Delivered',
            5 => 'Canceled',
            6 => 'Refund'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Get payment status label
     */
    private function getPaymentStatusLabel($status)
    {
        $labels = [
            1 => 'Paid',
            2 => 'Unpaid'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Export orders to Excel (optional)
     */
    public function export(Request $request)
    {
        // Implementation for exporting orders
        // You can use Laravel Excel package
    }

    /**
     * Print order invoice (optional)
     */
    public function printInvoice($id)
    {
        try {
            $order = Order::with([
                'user',
                'address',
                'orderProducts.product'
            ])->findOrFail($id);

            $order->order_status_label = $this->getOrderStatusLabel($order->order_status);
            $order->payment_status_label = $this->getPaymentStatusLabel($order->payment_status);

            return view('admin.orders.invoice', compact('order'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update order status (optional)
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:orders,id',
                'order_status' => 'required|integer|in:1,2,3,4,5,6',
            ]);

            $orders = Order::with('user')->whereIn('id', $request->order_ids)->get();

            DB::beginTransaction();

            foreach ($orders as $order) {
                $oldStatus = $order->order_status;
                
                $order->update([
                    'order_status' => $request->order_status
                ]);

                // Send notification for each order
                $this->sendOrderStatusNotification($order, $oldStatus, $request->order_status);
            }

            DB::commit();

            return back()->with('success', count($orders) . ' orders updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update orders: ' . $e->getMessage());
        }
    }
}