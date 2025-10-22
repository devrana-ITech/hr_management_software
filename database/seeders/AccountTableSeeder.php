<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $accounts = [
            // Assets
            ['account_id' => '1001', 'name' => 'Cash In Hand', 'slug' => 'Cash_&_Bank_Accounts', 'type' => 'asset', 'is_default' => 1, 'is_bank' => 1],
            ['account_id' => '1010', 'name' => 'Accounts Receivable', 'slug' => 'Accounts_Receivable', 'type' => 'asset', 'is_default' => 1],
            ['account_id' => '1020', 'name' => 'Inventory', 'type' => 'asset', 'is_default' => 0],
            ['account_id' => '1030', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'is_default' => 0],
            ['account_id' => '1040', 'name' => 'Short-term Investments', 'type' => 'asset', 'is_default' => 0],
            ['account_id' => '1050', 'name' => 'Employee Loans Receivable', 'slug' => 'Employee_Loans_Receivable', 'type' => 'asset', 'is_default' => 1],
            // Liabilities
            ['account_id' => '2001', 'name' => 'Accounts Payable', 'slug' => 'Accounts_Payable', 'type' => 'liability', 'is_default' => 1],
            ['account_id' => '2010', 'name' => 'Loans Payable', 'slug' => 'Loans_Payable', 'type' => 'liability', 'is_default' => 1],
            // Equity
            ['account_id' => '3001', 'name' => 'Common Stock', 'type' => 'equity', 'is_default' => 0],
            ['account_id' => '3010', 'name' => 'Owner Equity', 'slug' => 'Owner_Equity', 'type' => 'equity', 'is_default' => 1],
            // Revenue
            ['account_id' => '4001', 'name' => 'Sales Revenue', 'slug' => 'Sales_Revenue', 'type' => 'revenue', 'is_default' => 1],
            ['account_id' => '4010', 'name' => 'Service Revenue', 'slug' => 'Service_Revenue', 'type' => 'revenue', 'is_default' => 1],
            ['account_id' => '4020', 'name' => 'Interest Income', 'slug' => 'Interest_Income', 'type' => 'revenue', 'is_default' => 1],
            ['account_id' => '4030', 'name' => 'Other Income', 'slug' => 'Other_Income', 'type' => 'revenue', 'is_default' => 1],
            // Expenses
            ['account_id' => '5001', 'name' => 'Cost of Goods Sold', 'slug' => 'Cost_of_Goods_Sold', 'type' => 'expense', 'is_default' => 1],
            ['account_id' => '5010', 'name' => 'Salaries and Wages Expense', 'slug' => 'Salaries_and_Wages_Expense', 'type' => 'expense', 'is_default' => 1],
            ['account_id' => '5020', 'name' => 'Rent Expense', 'type' => 'expense', 'is_default' => 1],
            ['account_id' => '5030', 'name' => 'Utilities Expense', 'type' => 'expense', 'is_default' => 1],
            ['account_id' => '5040', 'name' => 'Depreciation Expense', 'type' => 'expense', 'is_default' => 0],
            ['account_id' => '5050', 'name' => 'Advertising Expense', 'type' => 'expense', 'is_default' => 0],
            ['account_id' => '5060', 'name' => 'Office Supplies Expense', 'type' => 'expense', 'is_default' => 0],
            ['account_id' => '5060', 'name' => 'Other Expenses', 'slug' => 'Other_Expenses', 'type' => 'expense', 'is_default' => 1],
        ];

        // Insert accounts into the database
        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
