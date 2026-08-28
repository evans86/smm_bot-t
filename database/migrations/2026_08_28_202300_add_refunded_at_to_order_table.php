<?php

use App\Models\Order\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRefundedAtToOrderTable extends Migration
{
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('status');
        });

        // Уже закрытые заказы не должны получить повторный возврат после выката.
        DB::table('order')->whereIn('status', [
            Order::CANCEL_STATUS,
            Order::FINISH_STATUS,
            Order::OLD_STATUS,
            Order::TO_PROCESS_STATUS,
        ])->update(['refunded_at' => now()]);
    }

    public function down()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn('refunded_at');
        });
    }
}
