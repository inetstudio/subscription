<?php

namespace InetStudio\Subscription\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * InetStudio\Subscription\Models\SubscriptionModel
 *
 * @property int $id
 * @property string $email
 * @property int $is_subscribed
 * @property string $user_id
 * @property array $additional_info
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel active()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel inactive()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Subscription\Models\SubscriptionModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereAdditionalInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Subscription\Models\SubscriptionModel whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Subscription\Models\SubscriptionModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Subscription\Models\SubscriptionModel withoutTrashed()
 * @mixin \Eloquent
 */
class SubscriptionModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'subscription';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'is_subscribed', 'user_id', 'additional_info',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы к базовым типам.
     *
     * @var array
     */
    protected $casts = [
        'additional_info' => 'array',
    ];

    /**
     * Заготовка запроса "Подписанные пользователи".
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('is_subscribed', 1);
    }

    /**
     * Заготовка запроса "Отписавшиеся пользователи".
     *
     * @param $query
     * @return mixed
     */
    public function scopeInactive($query)
    {
        return $query->where('is_subscribed', 0);
    }

    /**
     * Обратное отношение с моделью пользователя
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
