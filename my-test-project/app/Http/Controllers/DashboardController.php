<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $role = $request->query('role');

        $warehouseData = [
            'inventory_in_out' => [
                ['item' => 'Rice 50kg', 'in' => 120, 'out' => 95],
                ['item' => 'Cooking Oil 5L', 'in' => 60, 'out' => 40],
            ],
            'purchase_tracking' => [
                ['po_number' => 'PO-1001', 'supplier' => 'ABC Supplier', 'status' => 'Pending'],
                ['po_number' => 'PO-1002', 'supplier' => 'XYZ Trading', 'status' => 'Delivered'],
            ],
            'goods_expiry' => [
                ['item' => 'Milk Powder', 'expiry_date' => '2025-08-15'],
                ['item' => 'Canned Fish', 'expiry_date' => '2025-09-01'],
            ],
            'inventory_low_alert' => [
                ['item' => 'Sugar 1kg', 'remaining_qty' => 5],
                ['item' => 'Salt 1kg', 'remaining_qty' => 3],
            ],
        ];

        $accountsData = [
            'billing_for_supplier' => [
                ['invoice_no' => 'INV-S-001', 'supplier' => 'ABC Supplier', 'amount' => 1500.00],
                ['invoice_no' => 'INV-S-002', 'supplier' => 'XYZ Trading', 'amount' => 980.50],
            ],
            'billing_for_stock_out' => [
                ['invoice_no' => 'INV-O-001', 'customer' => 'Customer A', 'amount' => 320.00],
                ['invoice_no' => 'INV-O-002', 'customer' => 'Customer B', 'amount' => 410.75],
            ],
            'billing_for_internal_team' => [
                ['invoice_no' => 'INV-T-001', 'department' => 'Operations', 'amount' => 150.00],
                ['invoice_no' => 'INV-T-002', 'department' => 'Sales', 'amount' => 210.00],
            ],
        ];

        switch ($role) {
            case 'SUPER_ADMIN':
                return response()->json([
                    'role' => $role,
                    'warehouse' => $warehouseData,
                    'accounts' => $accountsData,
                ]);

            case 'WAREHOUSE_MANAGER':
                return response()->json([
                    'role' => $role,
                    'warehouse' => $warehouseData,
                ]);

            case 'ACCOUNTS':
                return response()->json([
                    'role' => $role,
                    'accounts' => $accountsData,
                ]);

            default:
                return response()->json([
                    'message' => 'Invalid or missing role. Use SUPER_ADMIN, WAREHOUSE_MANAGER, or ACCOUNTS.',
                ], 400);
        }
    }
}