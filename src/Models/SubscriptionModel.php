<?php

namespace InetStudio\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\ACL\Users\Models\Traits\HasUser;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;

/**
 * Class SubscriptionModel.
 */
class SubscriptionModel extends Model implements SubscriptionModelContract
{
    use HasUser;
    use SoftDeletes;
    use HasJSONColumns;
    use BuildQueryScopeTrait;

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
        'email', 'status', 'user_id', 'additional_info',
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
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id', 'email', 'status', 'user_id', 'additional_info',
        ];
    }

    /**
     * Сеттер атрибута email.
     *
     * @param $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута status.
     *
     * @param $value
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута user_id.
     *
     * @param $value
     */
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = (int) $value;
    }

    /**
     * Сеттер атрибута additional_info.
     *
     * @param $value
     */
    public function setAdditionalInfoAttribute($value)
    {
        $this->attributes['additional_info'] = json_encode((array) $value);
    }

    /**
     * Заготовка запроса "Ожидание подтверждения".
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Заготовка запроса "Подписанные пользователи".
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }

    /**
     * Заготовка запроса "Отписавшиеся пользователи".
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    /**
     * Заготовка запроса "Очищенные пользователи".
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeCleaned($query)
    {
        return $query->where('status', 'cleaned');
    }
}
