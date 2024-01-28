<?php

namespace App\Models\Order;

use App\Models\Bot\Bot;
use App\Models\Description\Country;
use App\Models\Proxy\Proxy;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const DEFAULT_TYPE = 'Default';
    const PACKAGE_TYPE = 'Package';
    const CUSTOM_COMMENTS_TYPE = 'Custom Comments';
    const MENTIONS_USER_FOLLOWERS_TYPE = 'Mentions User Followers';
    const CUSTOM_COMMENTS_PACKAGE_TYPE = 'Custom Comments Package';
    const POLL_TYPE = 'Poll';
    const SUBSCRIPTIONS_TYPE = 'Subscriptions';

    const CREATE_STATUS = 'Pending';
    const WORK_STATUS = 'In progress';
    const TO_PROCESS_STATUS = 'Partial';
    const TO_PROCESSING_STATUS = 'Processing';
    const FINISH_STATUS = 'Completed';
    const CANCEL_STATUS = 'Canceled'; //?
    const OLD_STATUS = 'Old';

    use HasFactory;

    protected $guarded = false;
    protected $table = 'order';

    public function bot()
    {
        return $this->hasOne(Bot::class, 'id', 'bot_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
