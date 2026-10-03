<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Method;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Adhira',
                'email' => 'admin@adhira.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $kasir = User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir Adhira',
                'email' => 'kasir@adhira.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ]
        );

        // Seed Methods
        $m1 = Method::firstOrCreate(['name' => 'Print Hitam Putih'], ['price' => 500]);
        $m2 = Method::firstOrCreate(['name' => 'Print Berwarna'], ['price' => 1500]);
        $m3 = Method::firstOrCreate(['name' => 'Fotocopy Hitam Putih'], ['price' => 300]);
        $m4 = Method::firstOrCreate(['name' => 'Fotocopy Berwarna'], ['price' => 1200]);

        // Seed Items
        Item::firstOrCreate(['name' => 'Kertas F4'], ['stock' => 120, 'price' => 500]);
        Item::firstOrCreate(['name' => 'Kertas A4'], ['stock' => 8, 'price' => 450]);

        // Seed Transactions for past 7 days if empty
        if (Transaction::count() === 0) {
            $methods = [$m1, $m2, $m3, $m4];
            $papers = ['F4', 'A4', 'A3', 'A5'];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $numTransactions = rand(5, 12);

                for ($t = 0; $t < $numTransactions; $t++) {
                    $trxCode = 'TRX-' . $date->format('Ymd') . '-' . str_pad($t + 1, 4, '0', STR_PAD_LEFT);
                    $method = $methods[array_rand($methods)];
                    $paper = $papers[array_rand($papers)];
                    $qty = rand(5, 50);
                    $total = $method->price * $qty;
                    $paymentMethod = (rand(0, 1) === 0) ? 'cash' : 'qris';
                    $paidAmount = $paymentMethod === 'cash' ? ceil($total / 5000) * 5000 : $total;
                    if ($paidAmount < $total) $paidAmount = $total;
                    $changeAmount = $paidAmount - $total;

                    $trx = Transaction::create([
                        'transaction_code' => $trxCode,
                        'user_id' => $kasir->id,
                        'total_amount' => $total,
                        'payment_method' => $paymentMethod,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $changeAmount,
                        'created_at' => $date->copy()->addMinutes(rand(1, 600)),
                        'updated_at' => $date->copy()->addMinutes(rand(1, 600)),
                    ]);

                    TransactionItem::create([
                        'transaction_id' => $trx->id,
                        'item_type' => 'method',
                        'method_id' => $method->id,
                        'name' => $method->name,
                        'paper_type' => $paper,
                        'qty' => $qty,
                        'price' => $method->price,
                        'subtotal' => $total,
                        'created_at' => $trx->created_at,
                        'updated_at' => $trx->updated_at,
                    ]);
                }
            }
        }
    }
}
