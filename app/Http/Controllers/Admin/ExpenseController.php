<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->search) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(category) LIKE ?', ["%{$search}%"]);
            });
        }

        $expenses = $query->latest('transaction_date')->paginate(20)->withQueryString();

        // Revenue Calculations
        $totalFees = Payment::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $profit = $totalFees - $totalExpenses;

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.expenses._table', compact('expenses'))->render(),
                'pagination' => $expenses->links()->render(),
                'total' => $expenses->total(),
                'stats' => [
                    'fees' => number_format($totalFees, 2),
                    'expenses' => number_format($totalExpenses, 2),
                    'profit' => number_format($profit, 2),
                ]
            ]);
        }

        return view('admin.expenses.index', compact('expenses', 'totalFees', 'totalExpenses', 'profit'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Expense::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense recorded successfully.']);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expense->update($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense updated successfully.']);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
